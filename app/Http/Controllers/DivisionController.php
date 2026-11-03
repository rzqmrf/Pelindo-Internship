<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Division::withCount(['interns' => function ($q) {
            $q->where('status', 'active');
        }, 'supervisors']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('head_name', 'like', "%{$search}%");
        }

        $divisions = $query->paginate(10);

        // Stats calculation
        $totalDivisions = Division::count();
        $totalQuota = Division::sum('quota');
        $filledQuota = \App\Models\InternProfile::where('status', 'active')->count();
        $remainingQuota = max(0, $totalQuota - $filledQuota);

        $pctFilled = $totalQuota > 0 ? round(($filledQuota / $totalQuota) * 100, 1) : 0;
        $pctRemaining = $totalQuota > 0 ? round(($remainingQuota / $totalQuota) * 100, 1) : 0;

        return view('admin.divisions.index', compact(
            'divisions',
            'totalDivisions',
            'totalQuota',
            'filledQuota',
            'remainingQuota',
            'pctFilled',
            'pctRemaining'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:divisions,code|max:20',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quota' => 'required|integer|min:1',
            'head_name' => 'nullable|string|max:255',
        ]);

        $division = Division::create($request->all());
        AuditLogger::record('division.create', 'divisions', "Divisi baru dibuat: {$division->name} ({$division->code}).", $division);

        return redirect()->back()->with('success', 'Divisi baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $division = Division::findOrFail($id);

        $request->validate([
            'code' => "required|string|max:20|unique:divisions,code,{$id}",
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quota' => 'required|integer|min:1',
            'head_name' => 'nullable|string|max:255',
        ]);

        $division->update($request->all());

        return redirect()->back()->with('success', 'Data divisi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $division = Division::findOrFail($id);
        if ($division->interns()->count() > 0) {
            return redirect()->back()->with('error', 'Divisi tidak dapat dihapus karena masih memiliki peserta magang.');
        }

        $division->delete();

        return redirect()->back()->with('success', 'Divisi berhasil dihapus.');
    }
}
