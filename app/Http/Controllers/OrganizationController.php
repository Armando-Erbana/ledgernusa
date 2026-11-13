<?php
namespace App\Http\Controllers;
use App\Models\Division;
use App\Models\Position;
use App\Models\ApprovalRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationController extends Controller
{
    public function index()
    {
        $companyId = session('company_id');
        $divisions = Division::where('company_id', $companyId)->with('positions')->get();
        $positions = Position::whereHas('division', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->get();
        $rules = ApprovalRule::where('company_id', $companyId)->with('position')->orderBy('document_type')->orderBy('level')->get();
        return view('settings.organization', compact('divisions', 'positions', 'rules'));
    }

    public function storeDivision(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Division::create([
            'company_id' => session('company_id'),
            'name' => $request->name
        ]);
        return back()->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function storePosition(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255'
        ]);
        Position::create([
            'division_id' => $request->division_id,
            'name' => $request->name
        ]);
        return back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function storeRule(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:purchase_order,sales_order',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'position_id' => 'required|exists:positions,id',
            'level' => 'required|integer|min:1'
        ]);

        ApprovalRule::create([
            'company_id' => session('company_id'),
            'document_type' => $request->document_type,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'position_id' => $request->position_id,
            'level' => $request->level,
        ]);

        return back()->with('success', 'Aturan persetujuan berhasil ditambahkan.');
    }

    public function destroyDivision(Division $division) { $division->delete(); return back(); }
    public function destroyPosition(Position $position) { $position->delete(); return back(); }
    public function destroyRule(ApprovalRule $rule) { $rule->delete(); return back(); }
}