<?php
namespace App\Services;
use App\Models\ApprovalRule;
use App\Models\ApprovalLog;

class ApprovalService
{
    public function checkAndSubmit($document, string $type, string $routeName)
    {
        $total = $document->total;
        $rule = ApprovalRule::where('company_id', $document->company_id)
            ->where('document_type', $type)
            ->where('min_amount', '<=', $total)
            ->where(function($q) use ($total) {
                $q->where('max_amount', '>=', $total)->orWhereNull('max_amount');
            })
            ->orderBy('level', 'asc')
            ->first();

        if (!$rule) return false;

        $document->update([
            'approval_status' => 'pending',
            'current_approver_position_id' => $rule->position_id,
        ]);

        $approvers = $rule->position->users;
        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\DocumentPendingNotification($document, $type, route($routeName, $document->id)));
        }
        return true;
    }

    public function process($document, $userId, $action, $notes = null)
    {
        ApprovalLog::create([
            'company_id' => $document->company_id,
            'approvable_id' => $document->id,
            'approvable_type' => get_class($document),
            'user_id' => $userId,
            'action' => $action,
            'notes' => $notes,
        ]);

        if ($action === 'approved') {
            $type = get_class($document) === 'App\Models\PurchaseOrder' ? 'purchase_order' : 'sales_order';
            $currentRule = ApprovalRule::where('company_id', $document->company_id)
                ->where('document_type', $type)
                ->where('position_id', $document->current_approver_position_id)
                ->first();
                
            $currentLevel = $currentRule ? $currentRule->level : 1;

            $nextRule = ApprovalRule::where('company_id', $document->company_id)
                ->where('document_type', $type)
                ->where('level', '>', $currentLevel)
                ->orderBy('level', 'asc')->first();

            if ($nextRule) {
                $document->update(['current_approver_position_id' => $nextRule->position_id]);
                $approvers = $nextRule->position->users;
                foreach ($approvers as $approver) {
                    $route = $type === 'purchase_order' ? 'purchase-orders.show' : 'sales-orders.show';
                    $approver->notify(new \App\Notifications\DocumentPendingNotification($document, $type, route($route, $document->id)));
                }
            } else {
                $document->update([
                    'approval_status' => 'approved',
                    'status' => 'confirmed',
                    'confirmed_by' => $userId,
                    'confirmed_at' => now(),
                ]);
            }
        } else {
            $document->update([
                'approval_status' => 'revision',
                'current_approver_position_id' => null,
            ]);
        }
    }
}