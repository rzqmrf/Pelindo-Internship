<?php

namespace App\Http\Controllers;

use App\Models\AllowanceClaim;
use App\Models\DailyActivity;
use App\Models\InternProfile;
use App\Models\Notification;
use App\Models\User;
use App\Models\Division;
use App\Services\AllowanceService;
use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AllowanceClaimController extends Controller
{
    protected AllowanceService $allowanceService;
    protected FileStorageService $fileStorageService;

    public function __construct(AllowanceService $allowanceService, FileStorageService $fileStorageService)
    {
        $this->allowanceService = $allowanceService;
        $this->fileStorageService = $fileStorageService;
    }

    /**
     * Display a listing of allowance claims for Intern.
     */
    public function index()
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        $claims = $user->allowanceClaims()->orderBy('period_number', 'desc')->get();
        $profile = $user->internProfile;

        $allPeriods = $this->allowanceService->getInternPeriods($user);
        
        // Filter periods that are completed and not yet claimed
        $availablePeriods = array_filter($allPeriods, function($p) {
            return !$p['claimed'] && !$p['in_progress'];
        });

        return view('intern.allowance.index', compact('claims', 'availablePeriods', 'profile'));
    }

    /**
     * Calculate period logs & allowance (AJAX API).
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $breakdown = $this->allowanceService->calculateBreakdown(
            Auth::id(),
            $request->start_date,
            $request->end_date
        );

        return response()->json($breakdown);
    }

    /**
     * Submit monthly/periodic allowance claim.
     */
    public function store(Request $request)
    {
        Carbon::setLocale('id');
        $request->validate([
            'period_value' => 'required|string', // format: "period_number|start_date|end_date"
            'attachment' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120',
        ]);

        $parts = explode('|', $request->period_value);
        if (count($parts) !== 3) {
            return redirect()->back()->withErrors(['period_value' => 'Format periode pengajuan tidak valid.']);
        }

        $periodNum = intval($parts[0]);
        $startDate = $parts[1];
        $endDate = $parts[2];

        $user = Auth::user();
        $profile = $user->internProfile;

        // Verify if bank info exists
        if (empty($profile->bank_name) || empty($profile->bank_account_number) || empty($profile->bank_account_holder)) {
            return redirect()->back()->withErrors(['bank' => 'Informasi rekening bank Anda belum lengkap di Profil. Silakan lengkapi profil terlebih dahulu sebelum mengajukan klaim.']);
        }

        // Check if already claimed and not rejected
        $exists = $user->allowanceClaims()
            ->where('period_number', $periodNum)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['period_value' => 'Klaim uang saku untuk periode ini sudah diajukan.']);
        }

        // Calculate attendance log statistics
        $activities = DailyActivity::where('intern_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'pending', 'revision'])
            ->get();

        $wfoCount = $activities->where('work_location', 'WFO')->count();
        $wfhCount = $activities->where('work_location', 'WFH')->count();
        $leaveCount = $activities->where('work_location', 'Leave')->count();

        $totalAllowance = ($wfoCount * $this->wfoRate) + ($wfhCount * $this->wfhRate);

        // Upload attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'claim_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/claims'), $filename);
            $attachmentPath = 'uploads/claims/' . $filename;
        }

        $claim = AllowanceClaim::create([
            'intern_id' => $user->id,
            'supervisor_id' => $profile->supervisor_id,
            'period_number' => $periodNum,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'wfo_count' => $wfoCount,
            'wfh_count' => $wfhCount,
            'leave_count' => $leaveCount,
            'total_allowance' => $totalAllowance,
            'bank_name' => $profile->bank_name,
            'bank_account_number' => $profile->bank_account_number,
            'bank_account_holder' => $profile->bank_account_holder,
            'attachment_path' => $attachmentPath,
            'status' => 'pending_supervisor',
        ]);

        $formattedPeriodLabel = 'Periode ' . $periodNum . ' (' . Carbon::parse($startDate)->isoFormat('D MMMM') . ' - ' . Carbon::parse($endDate)->isoFormat('D MMMM YYYY') . ')';

        // Notify Supervisor
        if ($profile->supervisor_id) {
            Notification::create([
                'user_id' => $profile->supervisor_id,
                'title' => 'Klaim Uang Saku Baru',
                'message' => $user->name . ' mengajukan klaim uang saku untuk ' . $formattedPeriodLabel . '.',
            ]);
        }

        return redirect()->route('intern.allowance.index')->with('success', 'Klaim uang saku bulanan berhasil diajukan ke Pembimbing.');
    }

    /**
     * Display allowance claims for Supervisor.
     */
    public function supervisorIndex()
    {
        Carbon::setLocale('id');
        $claims = Auth::user()->supervisedAllowanceClaims()
            ->with(['intern.internProfile'])
            ->where('status', 'pending_supervisor')
            ->orderBy('period_number', 'asc')
            ->get();

        return view('supervisor.allowance.index', compact('claims'));
    }

    /**
     * Approve allowance claim by Supervisor.
     */
    public function supervisorApprove(Request $request, $id)
    {
        Carbon::setLocale('id');
        $claim = Auth::user()->supervisedAllowanceClaims()->findOrFail($id);

        $claim->update([
            'status' => 'approved_by_supervisor',
            'supervisor_note' => $request->supervisor_note,
        ]);

        // Bulk-approve all daily activities of that intern in that period date range!
        DailyActivity::where('intern_id', $claim->intern_id)
            ->whereBetween('date', [$claim->start_date, $claim->end_date])
            ->whereIn('status', ['pending', 'revision'])
            ->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'supervisor_note' => $request->supervisor_note ?? 'Disetujui via persetujuan rekap uang saku periodik.',
            ]);

        $formattedPeriodLabel = 'Periode ' . $claim->period_number . ' (' . Carbon::parse($claim->start_date)->isoFormat('D MMMM') . ' - ' . Carbon::parse($claim->end_date)->isoFormat('D MMMM YYYY') . ')';

        // Notify Intern
        Notification::create([
            'user_id' => $claim->intern_id,
            'title' => 'Klaim Uang Saku Disetujui',
            'message' => 'Klaim uang saku ' . $formattedPeriodLabel . ' telah disetujui oleh pembimbing dan diteruskan ke HR Admin.',
        ]);

        // Notify HR Admins
        $hrAdmins = User::where('role', 'admin')->get();
        foreach ($hrAdmins as $hr) {
            Notification::create([
                'user_id' => $hr->id,
                'title' => 'Klaim Uang Saku Butuh Verifikasi',
                'message' => 'Klaim uang saku ' . $claim->intern?->name . ' ' . $formattedPeriodLabel . ' telah disetujui pembimbing dan siap dicairkan.',
            ]);
        }

        return redirect()->route('supervisor.allowance.index')->with('success', 'Klaim uang saku bulanan peserta magang berhasil disetujui.');
    }

    /**
     * Reject allowance claim by Supervisor.
     */
    public function supervisorReject(Request $request, $id)
    {
        Carbon::setLocale('id');
        $claim = Auth::user()->supervisedAllowanceClaims()->findOrFail($id);

        $claim->update([
            'status' => 'rejected',
            'supervisor_note' => $request->supervisor_note,
        ]);

        $formattedPeriodLabel = 'Periode ' . $claim->period_number . ' (' . Carbon::parse($claim->start_date)->isoFormat('D MMMM') . ' - ' . Carbon::parse($claim->end_date)->isoFormat('D MMMM YYYY') . ')';

        // Notify Intern
        Notification::create([
            'user_id' => $claim->intern_id,
            'title' => 'Klaim Uang Saku Ditolak',
            'message' => 'Klaim uang saku ' . $formattedPeriodLabel . ' ditolak oleh pembimbing. Catatan: ' . ($request->supervisor_note ?? '-'),
        ]);

        return redirect()->route('supervisor.allowance.index')->with('success', 'Klaim uang saku bulanan peserta magang berhasil ditolak.');
    }

    /**
     * Display allowance claims for HR Admin.
     */
    public function hrIndex(Request $request)
    {
        Carbon::setLocale('id');
        $query = AllowanceClaim::with(['intern.internProfile.division', 'supervisor']);

        if ($request->filled('division_id')) {
            $query->whereHas('intern.internProfile', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->orderBy('period_number', 'desc')->paginate(12);
        $divisions = Division::all();

        return view('admin.allowance.index', compact('claims', 'divisions'));
    }

    /**
     * Process / Mark as paid by HR Admin.
     */
    public function hrProcess(Request $request, $id)
    {
        Carbon::setLocale('id');
        $claim = AllowanceClaim::findOrFail($id);

        $claim->update([
            'status' => 'processed_by_hr',
            'hr_note' => $request->hr_note,
        ]);

        $formattedPeriodLabel = 'Periode ' . $claim->period_number . ' (' . Carbon::parse($claim->start_date)->isoFormat('D MMMM') . ' - ' . Carbon::parse($claim->end_date)->isoFormat('D MMMM YYYY') . ')';

        // Notify Intern
        Notification::create([
            'user_id' => $claim->intern_id,
            'title' => 'Uang Saku Dicairkan! 💸',
            'message' => 'Klaim uang saku Anda untuk ' . $formattedPeriodLabel . ' senilai Rp ' . number_format($claim->total_allowance, 0, ',', '.') . ' telah dicairkan oleh HR Admin ke rekening ' . $claim->bank_name . '.',
        ]);

        return redirect()->route('admin.allowance.index')->with('success', 'Klaim uang saku berhasil ditandai sebagai dicairkan.');
    }

    /**
     * Bulk Process / Mark as paid by HR Admin.
     */
    public function bulkProcessHr(Request $request)
    {
        Carbon::setLocale('id');
        $request->validate([
            'claim_ids' => 'required|array',
            'claim_ids.*' => 'exists:allowance_claims,id',
        ]);

        $claims = AllowanceClaim::whereIn('id', $request->claim_ids)->get();
        $count = 0;

        foreach ($claims as $claim) {
            if ($claim->status !== 'processed_by_hr') {
                $claim->update([
                    'status' => 'processed_by_hr',
                    'hr_note' => 'Pencairan massal via HR Payroll Portal',
                ]);

                $formattedPeriodLabel = 'Periode ' . $claim->period_number . ' (' . Carbon::parse($claim->start_date)->isoFormat('D MMMM') . ' - ' . Carbon::parse($claim->end_date)->isoFormat('D MMMM YYYY') . ')';

                Notification::sendToUser(
                    $claim->intern_id,
                    'Uang Saku Dicairkan! 💸',
                    'Klaim uang saku Anda untuk ' . $formattedPeriodLabel . ' senilai Rp ' . number_format($claim->total_allowance, 0, ',', '.') . ' telah dicairkan oleh HR Admin ke rekening ' . $claim->bank_name . '.'
                );

                $count++;
            }
        }

        return redirect()->route('admin.allowance.index')->with('success', "Sebanyak {$count} klaim uang saku berhasil dicairkan secara massal.");
    }

    /**
     * Export Payroll Allowance Rekap to CSV/Excel format for HR Admin.
     */
    public function exportPayrollExcel(Request $request)
    {
        Carbon::setLocale('id');
        $query = AllowanceClaim::with(['intern.internProfile.division']);

        if ($request->filled('division_id')) {
            $query->whereHas('intern.internProfile', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->orderBy('period_number', 'desc')->get();

        $filename = 'Rekap_Uang_Saku_Pelindo_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($claims) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Column titles
            fputcsv($file, [
                'ID Klaim',
                'NIK',
                'Nama Intern',
                'NIM / NIS',
                'Divisi / Unit Kerja',
                'Periode Ke',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Hari WFO',
                'Hari WFH',
                'Hari Izin',
                'Total Uang Saku (Rp)',
                'Nama Bank',
                'Nomor Rekening',
                'Atas Nama Rekening',
                'Status Klaim',
                'Tanggal Pengajuan',
            ]);

            foreach ($claims as $c) {
                $profile = $c->intern?->internProfile;
                fputcsv($file, [
                    $c->id,
                    $profile?->nik_number ?? '-',
                    $c->intern?->name ?? '-',
                    $profile?->student_id_number ?? '-',
                    $profile?->division?->name ?? 'Divisi',
                    'Periode ' . $c->period_number,
                    $c->start_date ? Carbon::parse($c->start_date)->format('d/m/Y') : '-',
                    $c->end_date ? Carbon::parse($c->end_date)->format('d/m/Y') : '-',
                    $c->wfo_count,
                    $c->wfh_count,
                    $c->leave_count,
                    $c->total_allowance,
                    $c->bank_name,
                    "'" . $c->bank_account_number, // Single quote to prevent Excel scientific notation
                    $c->bank_account_holder,
                    strtoupper($c->status),
                    $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
