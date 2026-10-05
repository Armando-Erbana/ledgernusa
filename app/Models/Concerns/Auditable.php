<?php
namespace App\Models\Concerns;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::writeAudit('create', $model, null, self::cleanAttributes($model->getAttributes()));
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            if (empty($changes)) return;

            $old = [];
            foreach (array_keys($changes) as $key) {
                $old[$key] = $model->getOriginal($key);
            }
            self::writeAudit('update', $model, $old, $changes);
        });

        static::deleted(function ($model) {
            self::writeAudit('delete', $model, self::cleanAttributes($model->getAttributes()), null);
        });
    }

    protected static function cleanAttributes(array $attributes): array
    {
        // Sembunyikan field sensitif
        foreach (['password', 'remember_token'] as $field) {
            unset($attributes[$field]);
        }
        return $attributes;
    }

    protected static function writeAudit(string $action, $model, ?array $old, ?array $new): void
    {
        if (!auth()->check()) return;

        try {
            AuditLog::create([
                'company_id' => session('company_id') ?? ($model->company_id ?? null),
                'user_id' => auth()->id(),
                'action' => $action,
                'table_name' => $model->getTable(),
                'record_id' => $model->getKey(),
                'old_values' => $old,
                'new_values' => $new,
                'ip' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Exception $e) {
            // Jangan sampai audit log gagal bikin transaksi utama gagal
            \Log::warning('Audit log failed: ' . $e->getMessage());
        }
    }
}