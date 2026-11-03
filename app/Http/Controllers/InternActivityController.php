<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\InternProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InternActivityController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // 1. Today's Activity Check
        $todayActivity = DailyActivity::where('intern_id', $userId)
            ->whereDate('date', $today)
            ->first();

        // 2. Stats
        $totalSubmitted = DailyActivity::where('intern_id', $userId)
            ->where('status', '!=', 'draft')
            ->count();

        $pendingApprovalCount = DailyActivity::where('intern_id', $userId)
            ->where('status', 'pending')
            ->count();

        $approvedCount = DailyActivity::where('intern_id', $userId)
            ->where('status', 'approved')
            ->count();

        $revisionCount = DailyActivity::where('intern_id', $userId)
            ->where('status', 'revision')
            ->count();

        // 3. Recent Activities (Latest 5)
        $recentActivities = DailyActivity::where('intern_id', $userId)
            ->latest('date')
            ->take(5)
            ->get();

        // 4. Intern Profile & Supervisor
        $internProfile = InternProfile::with(['division', 'supervisor.supervisorProfile'])
            ->where('user_id', $userId)
            ->first();

        // 5. Announcements (Latest 3)
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(3)->get();

        // 6. Today's Attendance & Certificate
        $todayAttendance = \App\Models\Attendance::where('intern_id', $userId)
            ->whereDate('date', $today)
            ->first();

        $certificate = \App\Models\Certificate::where('intern_id', $userId)
            ->where('status', 'published')
            ->first();

        return view('intern.dashboard', compact(
            'todayActivity',
            'todayAttendance',
            'certificate',
            'totalSubmitted',
            'pendingApprovalCount',
            'approvedCount',
            'revisionCount',
            'recentActivities',
            'internProfile',
            'announcements'
        ));
    }

    // Riwayat Aktivitas
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = DailyActivity::where('intern_id', $userId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('work_location')) {
            $query->where('work_location', $request->work_location);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereYear('date', $date->year)->whereMonth('date', $date->month);
        }

        $activities = $query->latest('date')->paginate(12);

        return view('intern.activities.index', compact('activities'));
    }

    // Form Tambah Aktivitas Harian
    public function create()
    {
        $today = Carbon::today()->format('Y-m-d');
        return view('intern.activities.create', compact('today'));
    }

    // Simpan Aktivitas Harian (Draft / Submit)
    public function store(Request $request)
    {
        $userId = Auth::id();
        $internProfile = InternProfile::where('user_id', $userId)->first();

        $isSubmit = $request->action === 'submit';

        $request->validate([
            'date' => 'required|date',
            'work_location' => 'required|in:WFO,WFH,WFA,Leave',
            'activity_description' => $isSubmit ? 'required|string|min:10' : 'nullable|string',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'action' => 'required|in:draft,submit',
        ]);

        // Check if an activity for this date already exists
        $existing = DailyActivity::where('intern_id', $userId)
            ->whereDate('date', $request->date)
            ->first();

        if ($existing && $existing->status !== 'draft') {
            return redirect()->back()->with('error', 'Anda sudah mengirimkan aktivitas harian untuk tanggal ini. Silakan perbarui entri jika berstatus revisi.');
        }

        $attachmentPath = $existing?->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $status = $request->action === 'draft' ? 'draft' : 'pending';

        if ($existing) {
            $existing->update([
                'supervisor_id' => $internProfile?->supervisor_id ?? $existing->supervisor_id,
                'work_location' => $request->work_location,
                'activity_description' => $request->activity_description ?? '',
                'notes' => $request->notes,
                'attachment_path' => $attachmentPath,
                'status' => $status,
            ]);
        } else {
            DailyActivity::create([
                'intern_id' => $userId,
                'supervisor_id' => $internProfile?->supervisor_id,
                'date' => $request->date,
                'work_location' => $request->work_location,
                'activity_description' => $request->activity_description ?? '',
                'notes' => $request->notes,
                'attachment_path' => $attachmentPath,
                'status' => $status,
            ]);
        }

        $message = $status === 'draft' 
            ? 'Aktivitas harian berhasil disimpan sebagai DRAFT.' 
            : 'Aktivitas harian berhasil DISUBMIT ke Pembimbing.';

        if ($status === 'pending' && $internProfile?->supervisor_id) {
            \App\Models\Notification::create([
                'user_id' => $internProfile->supervisor_id,
                'title' => 'Logbook Harian Baru',
                'message' => Auth::user()->name . ' telah mengirimkan logbook baru untuk tanggal ' . Carbon::parse($request->date)->isoFormat('D MMMM YYYY') . '.',
            ]);
        }

        return redirect()->route('intern.activities.index')->with('success', $message);
    }

    // Form Edit Aktivitas (Allowed only for draft / revision)
    public function edit($id)
    {
        $userId = Auth::id();
        $activity = DailyActivity::where('id', $id)->where('intern_id', $userId)->firstOrFail();

        if (!in_array($activity->status, ['draft', 'revision'])) {
            return redirect()->route('intern.activities.index')
                ->with('error', 'Aktivitas yang telah disetujui atau sedang dalam proses tinjauan tidak dapat diedit.');
        }

        return view('intern.activities.edit', compact('activity'));
    }

    // Update Aktivitas
    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $activity = DailyActivity::where('id', $id)->where('intern_id', $userId)->firstOrFail();

        if (!in_array($activity->status, ['draft', 'revision'])) {
            return redirect()->route('intern.activities.index')
                ->with('error', 'Aktivitas tidak dapat diubah.');
        }

        $isSubmit = $request->action === 'submit';

        $request->validate([
            'date' => 'required|date',
            'work_location' => 'required|in:WFO,WFH,WFA,Leave',
            'activity_description' => $isSubmit ? 'required|string|min:10' : 'nullable|string',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'action' => 'required|in:draft,submit',
        ]);

        $data = [
            'date' => $request->date,
            'work_location' => $request->work_location,
            'activity_description' => $request->activity_description ?? '',
            'notes' => $request->notes,
            'status' => $request->action === 'draft' ? 'draft' : 'pending',
        ];

        if ($request->hasFile('attachment')) {
            if ($activity->attachment_path) {
                Storage::disk('public')->delete($activity->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'public');
        }

        $activity->update($data);

        $message = $data['status'] === 'draft' 
            ? 'Aktivitas harian berhasil diperbarui sebagai DRAFT.' 
            : 'Aktivitas harian berhasil DISUBMIT ulang ke Pembimbing.';

        if ($data['status'] === 'pending' && $activity->supervisor_id) {
            \App\Models\Notification::create([
                'user_id' => $activity->supervisor_id,
                'title' => 'Logbook Harian Diperbarui',
                'message' => Auth::user()->name . ' telah mengirimkan ulang logbook untuk tanggal ' . Carbon::parse($request->date)->isoFormat('D MMMM YYYY') . '.',
            ]);
        }

        return redirect()->route('intern.activities.index')->with('success', $message);
    }
}
