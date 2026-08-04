<?php

namespace App\Http\Controllers;

use App\Models\AllowanceClaim;
use App\Models\DailyActivity;
use App\Models\InternProfile;
use App\Models\Notification;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AllowanceClaimController extends Controller
{
    // Rates configuration
    private $wfoRate = 100000; // Rp 100.000 / hari
    private $wfhRate = 80000;  // Rp 80.000 / hari

    /**
     * Helper to get all 20-weekday periods for an intern.
     */
    private function getInternPeriods($user)
    {
        $profile = $user->internProfile;
        if (!$profile || !$profile->start_date) {
            return [];
        }

        $startDate = Carbon::parse($profile->start_date);
        $endDateLimit = Carbon::parse($profile->end_date ?? now());
        if ($endDateLimit->gt(now())) {
            $endDateLimit = now();
        }

        $periods = [];
        $currentDate = $startDate->copy();
        $periodNum = 1;

        // Loop and chunk into 20 weekdays periods
        while ($currentDate->lte($endDateLimit)) {
            $periodStartDate = $currentDate->copy();
            
            // Find the 20th weekday starting from $currentDate
            $weekdayCount = 0;
            $periodEndDate = $currentDate->copy();
            
            while ($weekdayCount < 20 && $periodEndDate->lte($endDateLimit)) {
                if (!$periodEndDate->isWeekend()) {
                    $weekdayCount++;
                }
                if ($weekdayCount < 20) {
                    $periodEndDate->addDay();
                }
            }

            // Check if the 20 weekdays are fully reached
            if ($weekdayCount === 20) {
                // Check if already claimed and not rejected
                $exists = $user->allowanceClaims()
                    ->where('period_number', $periodNum)
                    ->where('status', '!=', 'rejected')
                    ->exists();

                $periods[] = [
                    'number' => $periodNum,
                    'start_date' => $periodStartDate->format('Y-m-d'),
                    'end_date' => $periodEndDate->format('Y-m-d'),
                    'label' => "Periode " . $periodNum . " (" . $periodStartDate->isoFormat('D MMMM') . " - " . $periodEndDate->isoFormat('D MMMM YYYY') . ")",
                    'claimed' => $exists,
                    'in_progress' => false,
                ];
            } else {
                // In progress
                $periods[] = [
                    'number' => $periodNum,
                    'start_date' => $periodStartDate->format('Y-m-d'),
                    'end_date' => $periodEndDate->format('Y-m-d'),
                    'label' => "Periode " . $periodNum . " (" . $periodStartDate->isoFormat('D MMMM') . " - " . $periodEndDate->isoFormat('D MMMM YYYY') . ") [Berjalan]",
                    'claimed' => false,
                    'in_progress' => true,
                ];
                break; // Break since the current period is in progress
            }

            // Move to the next weekday after $periodEndDate
            $currentDate = $periodEndDate->copy()->addDay();
            $periodNum++;
        }

        return $periods;
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

        $allPeriods = $this->getInternPeriods($user);
        
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

        $userId = Auth::id();

        $activities = DailyActivity::where('intern_id', $userId)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get();

        $wfoCount = $activities->where('work_location', 'WFO')->count();
        $wfhCount = $activities->where('work_location', 'WFH')->count();
        $leaveCount = $activities->where('work_location', 'Leave')->count();

        $totalAllowance = ($wfoCount * $this->wfoRate) + ($wfhCount * $this->wfhRate);

        return response()->json([
            'wfo_count' => $wfoCount,
            'wfh_count' => $wfhCount,
            'leave_count' => $leaveCount,
            'wfo_rate_formatted' => 'Rp ' . number_format($this->wfoRate, 0, ',', '.'),
            'wfh_rate_formatted' => 'Rp ' . number_format($this->wfhRate, 0, ',', '.'),
            'total_allowance' => $totalAllowance,
            'total_allowance_formatted' => 'Rp ' . number_format($totalAllowance, 0, ',', '.'),
        ]);
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
}
