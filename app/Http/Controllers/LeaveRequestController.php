<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\DailyActivity;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display intern's own leave requests.
     */
    public function index()
    {
        Carbon::setLocale('id');
        $leaves = Auth::user()->leaveRequests()->orderBy('start_date', 'desc')->get();
        return view('intern.leaves.index', compact('leaves'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        Carbon::setLocale('id');
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string|in:izin,sakit',
            'reason' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:5120', // Max 5MB
        ], [
            'reason.min' => 'Alasan minimal harus berisi 10 karakter.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        // Check date range overlap
        $overlap = LeaveRequest::where('intern_id', Auth::id())
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($request) {
                $query->where('start_date', '<=', $request->end_date)
                      ->where('end_date', '>=', $request->start_date);
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->withErrors(['start_date' => 'Rentang tanggal izin yang diajukan bertabrakan dengan pengajuan izin/sakit Anda yang sudah ada.'])->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $internProfile = Auth::user()->internProfile;
        $supervisorId = $internProfile?->supervisor_id;

        $leave = LeaveRequest::create([
            'intern_id' => Auth::id(),
            'supervisor_id' => $supervisorId,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        // Notify supervisor
        if ($supervisorId) {
            $startDateFormatted = Carbon::parse($request->start_date)->isoFormat('D MMMM YYYY');
            $endDateFormatted = Carbon::parse($request->end_date)->isoFormat('D MMMM YYYY');
            $dateRangeStr = ($request->start_date === $request->end_date)
                ? $startDateFormatted
                : $startDateFormatted . ' s.d. ' . $endDateFormatted;

            Notification::create([
                'user_id' => $supervisorId,
                'title' => 'Pengajuan Izin Baru',
                'message' => Auth::user()->name . ' mengajukan ' . ($request->type === 'sakit' ? 'izin sakit' : 'izin mandiri') . ' untuk tanggal ' . $dateRangeStr . '.',
            ]);
        }

        return redirect()->route('intern.leaves.index')->with('success', 'Pengajuan izin berhasil diajukan dan sedang menunggu persetujuan pembimbing.');
    }

    /**
     * Cancel a pending leave request by intern.
     */
    public function cancel($id)
    {
        $leave = LeaveRequest::where('id', $id)
            ->where('intern_id', Auth::id())
            ->firstOrFail();

        if ($leave->status !== 'pending') {
            return redirect()->route('intern.leaves.index')
                ->with('error', 'Hanya pengajuan izin berstatus menunggu yang dapat dibatalkan.');
        }

        $leave->delete();

        return redirect()->route('intern.leaves.index')
            ->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }

    /**
     * Display leave requests for supervisor (with status history filter & delegation).
     */
    public function supervisorIndex(Request $request)
    {
        Carbon::setLocale('id');
        $supervisorIds = Auth::user()->accessibleSupervisorIds();
        $status = $request->input('status', 'pending');

        $query = LeaveRequest::with('intern.internProfile')
            ->whereIn('supervisor_id', $supervisorIds);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $leaves = $query->orderBy('start_date', 'desc')->get();

        return view('supervisor.leaves.index', compact('leaves', 'status'));
    }

    /**
     * Approve a leave request.
     */
    public function approve(Request $request, $id)
    {
        Carbon::setLocale('id');
        $supervisorIds = Auth::user()->accessibleSupervisorIds();
        $leave = LeaveRequest::where('id', $id)
            ->whereIn('supervisor_id', $supervisorIds)
            ->firstOrFail();

        $this->processApproval($leave, $request->supervisor_note);

        return redirect()->route('supervisor.leaves.index')->with('success', 'Pengajuan izin peserta magang berhasil disetujui.');
    }

    /**
     * Reject a leave request.
     */
    public function reject(Request $request, $id)
    {
        Carbon::setLocale('id');
        $supervisorIds = Auth::user()->accessibleSupervisorIds();
        $leave = LeaveRequest::where('id', $id)
            ->whereIn('supervisor_id', $supervisorIds)
            ->firstOrFail();

        $this->processRejection($leave, $request->supervisor_note);

        return redirect()->route('supervisor.leaves.index')->with('success', 'Pengajuan izin peserta magang berhasil ditolak.');
    }

    /**
     * Admin HR view & override for all leave requests across divisions.
     */
    public function adminIndex(Request $request)
    {
        Carbon::setLocale('id');
        $status = $request->input('status', 'pending');

        $query = LeaveRequest::with(['intern.internProfile.division', 'supervisor']);
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $leaves = $query->latest()->paginate(15)->withQueryString();

        return view('admin.leaves.index', compact('leaves', 'status'));
    }

    public function adminApprove(Request $request, $id)
    {
        Carbon::setLocale('id');
        $leave = LeaveRequest::findOrFail($id);
        $this->processApproval($leave, $request->supervisor_note ?: 'Disetujui oleh Admin HR (Override).');

        return redirect()->route('admin.leaves.index')->with('success', 'Pengajuan izin berhasil disetujui oleh Admin HR.');
    }

    public function adminReject(Request $request, $id)
    {
        Carbon::setLocale('id');
        $leave = LeaveRequest::findOrFail($id);
        $this->processRejection($leave, $request->supervisor_note ?: 'Ditolak oleh Admin HR.');

        return redirect()->route('admin.leaves.index')->with('success', 'Pengajuan izin berhasil ditolak oleh Admin HR.');
    }

    private function processApproval(LeaveRequest $leave, ?string $note): void
    {
        $leave->update([
            'status' => 'approved',
            'supervisor_note' => $note,
        ]);

        // Auto-create or update approved DailyActivities for all weekdays in the date range
        $startDate = Carbon::parse($leave->start_date);
        $endDate = Carbon::parse($leave->end_date);
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            if (!$currentDate->isWeekend()) {
                DailyActivity::updateOrCreate(
                    [
                        'intern_id' => $leave->intern_id,
                        'date' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'supervisor_id' => $leave->supervisor_id,
                        'work_location' => 'Leave',
                        'activity_description' => 'Izin (' . ucfirst($leave->type) . '): ' . $leave->reason,
                        'notes' => 'Surat bukti pengajuan izin terlampir.',
                        'attachment_path' => $leave->attachment_path,
                        'status' => 'approved',
                        'supervisor_note' => $leave->supervisor_note ?? 'Izin disetujui.',
                        'reviewed_at' => now(),
                    ]
                );
            }
            $currentDate->addDay();
        }

        $startDateFormatted = Carbon::parse($leave->start_date)->isoFormat('D MMMM YYYY');
        $endDateFormatted = Carbon::parse($leave->end_date)->isoFormat('D MMMM YYYY');
        $dateRangeStr = ($leave->start_date->format('Y-m-d') === $leave->end_date->format('Y-m-d'))
            ? $startDateFormatted
            : $startDateFormatted . ' s.d. ' . $endDateFormatted;

        Notification::create([
            'user_id' => $leave->intern_id,
            'title' => 'Pengajuan Izin Disetujui',
            'message' => 'Pengajuan ' . ($leave->type === 'sakit' ? 'izin sakit' : 'izin mandiri') . ' Anda untuk tanggal ' . $dateRangeStr . ' telah disetujui.',
        ]);
    }

    private function processRejection(LeaveRequest $leave, ?string $note): void
    {
        $leave->update([
            'status' => 'rejected',
            'supervisor_note' => $note,
        ]);

        $startDateFormatted = Carbon::parse($leave->start_date)->isoFormat('D MMMM YYYY');
        $endDateFormatted = Carbon::parse($leave->end_date)->isoFormat('D MMMM YYYY');
        $dateRangeStr = ($leave->start_date->format('Y-m-d') === $leave->end_date->format('Y-m-d'))
            ? $startDateFormatted
            : $startDateFormatted . ' s.d. ' . $endDateFormatted;

        Notification::create([
            'user_id' => $leave->intern_id,
            'title' => 'Pengajuan Izin Ditolak',
            'message' => 'Pengajuan ' . ($leave->type === 'sakit' ? 'izin sakit' : 'izin mandiri') . ' Anda untuk tanggal ' . $dateRangeStr . ' ditolak. Catatan: ' . ($note ?? '-'),
        ]);
    }
}
