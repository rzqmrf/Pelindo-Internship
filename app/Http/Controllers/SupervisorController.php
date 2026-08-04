<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InternProfile;
use App\Models\DailyActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        $supervisorId = Auth::id();

        // 1. My Interns
        $myInternsCount = InternProfile::where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->count();

        // 2. Pending Approvals Count for supervisor's interns
        $pendingApprovalsCount = DailyActivity::where('supervisor_id', $supervisorId)
            ->where('status', 'pending')
            ->count();

        // 3. Today's Submissions Count
        $todaysActivitiesCount = DailyActivity::where('supervisor_id', $supervisorId)
            ->whereDate('date', Carbon::today())
            ->count();

        // 4. Pending Approval Activities Queue (top 7)
        $pendingQueue = DailyActivity::with(['intern.internProfile'])
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'pending')
            ->latest()
            ->take(7)
            ->get();

        // 5. Activity Timeline (Recent approved/revised/rejected/pending updates)
        $activityTimeline = DailyActivity::with(['intern.internProfile'])
            ->where('supervisor_id', $supervisorId)
            ->latest('updated_at')
            ->take(10)
            ->get();

        // 6. Supervised Interns Summary List
        $supervisedInterns = InternProfile::with(['user', 'division'])
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->get();

        // 7. Announcements (Latest 3)
        $announcements = \App\Models\Announcement::with('creator')->latest()->take(3)->get();

        return view('supervisor.dashboard', compact(
            'myInternsCount',
            'pendingApprovalsCount',
            'todaysActivitiesCount',
            'pendingQueue',
            'activityTimeline',
            'supervisedInterns',
            'announcements'
        ));
    }

    // List Peserta Bimbingan
    public function myInterns(Request $request)
    {
        $supervisorId = Auth::id();
        $query = InternProfile::with(['user.dailyActivities', 'division'])
            ->where('supervisor_id', $supervisorId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('student_id_number', 'like', "%{$search}%")
              ->orWhere('institution', 'like', "%{$search}%");
        }

        $interns = $query->latest()->paginate(10);

        // Summary metrics
        $totalSupervisedInterns = InternProfile::where('supervisor_id', $supervisorId)->where('status', 'active')->count();
        $approvedReportsCount = DailyActivity::where('supervisor_id', $supervisorId)->where('status', 'approved')->count();
        $pendingReportsCount = DailyActivity::where('supervisor_id', $supervisorId)->where('status', 'pending')->count();
        $revisionReportsCount = DailyActivity::where('supervisor_id', $supervisorId)->where('status', 'revision')->count();

        return view('supervisor.interns', compact(
            'interns',
            'totalSupervisedInterns',
            'approvedReportsCount',
            'pendingReportsCount',
            'revisionReportsCount'
        ));
    }

    // Monitoring & Approval Queue
    public function activities(Request $request)
    {
        $supervisorId = Auth::id();
        $query = DailyActivity::with(['intern.internProfile.division'])
            ->where('supervisor_id', $supervisorId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }

        if ($request->filled('work_location')) {
            $query->where('work_location', $request->work_location);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $activities = $query->latest('date')->paginate(15);

        // List of supervised interns for filter dropdown
        $myInterns = User::whereHas('internProfile', function ($q) use ($supervisorId) {
            $q->where('supervisor_id', $supervisorId);
        })->get();

        // Calculate KPI stats
        $totalActivities = DailyActivity::where('supervisor_id', $supervisorId)->count();
        $approvedCount = DailyActivity::where('supervisor_id', $supervisorId)->where('status', 'approved')->count();
        $pendingCount = DailyActivity::where('supervisor_id', $supervisorId)->where('status', 'pending')->count();
        $revisionCount = DailyActivity::where('supervisor_id', $supervisorId)->where('status', 'revision')->count();
        $rejectedCount = DailyActivity::where('supervisor_id', $supervisorId)->where('status', 'rejected')->count();

        $pctApproved = $totalActivities > 0 ? round(($approvedCount / $totalActivities) * 100, 1) : 0;
        $pctPending = $totalActivities > 0 ? round(($pendingCount / $totalActivities) * 100, 1) : 0;
        $pctRevision = $totalActivities > 0 ? round(($revisionCount / $totalActivities) * 100, 1) : 0;
        $pctRejected = $totalActivities > 0 ? round(($rejectedCount / $totalActivities) * 100, 1) : 0;

        return view('supervisor.activities', compact(
            'activities', 
            'myInterns',
            'totalActivities',
            'approvedCount',
            'pendingCount',
            'revisionCount',
            'rejectedCount',
            'pctApproved',
            'pctPending',
            'pctRevision',
            'pctRejected'
        ));
    }

    // Approval Workflow Action: Approve, Revision, Reject
    public function approve(Request $request, $id)
    {
        $supervisorId = Auth::id();
        $activity = DailyActivity::where('id', $id)
            ->where('supervisor_id', $supervisorId)
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:approved,revision,rejected',
            'supervisor_note' => 'nullable|string',
        ]);

        if (in_array($request->status, ['revision', 'rejected']) && empty($request->supervisor_note)) {
            return redirect()->back()->with('error', 'Catatan pembimbing wajib diisi jika status Revisi atau Ditolak.');
        }

        $activity->update([
            'status' => $request->status,
            'supervisor_note' => $request->supervisor_note,
            'reviewed_at' => Carbon::now(),
        ]);

        $statusText = match ($request->status) {
            'approved' => 'disetujui',
            'revision' => 'diminta revisi',
            'rejected' => 'ditolak',
        };

        $statusTitle = match ($request->status) {
            'approved' => 'Logbook Disetujui',
            'revision' => 'Permintaan Revisi Logbook',
            'rejected' => 'Logbook Ditolak',
        };

        $statusMsg = match ($request->status) {
            'approved' => 'Logbook Anda untuk tanggal ' . Carbon::parse($activity->date)->isoFormat('D MMMM YYYY') . ' telah disetujui oleh pembimbing.',
            'revision' => 'Pembimbing meminta revisi pada logbook tanggal ' . Carbon::parse($activity->date)->isoFormat('D MMMM YYYY') . '. Catatan: ' . $request->supervisor_note,
            'rejected' => 'Logbook Anda untuk tanggal ' . Carbon::parse($activity->date)->isoFormat('D MMMM YYYY') . ' ditolak oleh pembimbing. Catatan: ' . $request->supervisor_note,
        };

        \App\Models\Notification::create([
            'user_id' => $activity->intern_id,
            'title' => $statusTitle,
            'message' => $statusMsg,
        ]);

        return redirect()->back()->with('success', "Aktivitas harian berhasil {$statusText}.");
    }

    // Supervisor Statistics View
    public function statistics()
    {
        $supervisorId = Auth::id();
        $interns = InternProfile::with(['user.dailyActivities'])->where('supervisor_id', $supervisorId)->get();

        $statsPerIntern = [];
        $totalReportsAll = 0;
        $totalApprovedAll = 0;
        $totalPendingAll = 0;
        $totalRevisionAll = 0;

        foreach ($interns as $intern) {
            $totalSubmitted = $intern->user?->dailyActivities ? $intern->user->dailyActivities->count() : 0;
            $approvedCount = $intern->user?->dailyActivities ? $intern->user->dailyActivities->where('status', 'approved')->count() : 0;
            $pendingCount = $intern->user?->dailyActivities ? $intern->user->dailyActivities->where('status', 'pending')->count() : 0;
            $revisionCount = $intern->user?->dailyActivities ? $intern->user->dailyActivities->where('status', 'revision')->count() : 0;

            $totalReportsAll += $totalSubmitted;
            $totalApprovedAll += $approvedCount;
            $totalPendingAll += $pendingCount;
            $totalRevisionAll += $revisionCount;

            // Generate 30 days activity heatmap data
            $heatmap = [];
            for ($i = 29; $i >= 0; $i--) {
                $targetDate = Carbon::today()->subDays($i);
                $dateStr = $targetDate->format('Y-m-d');
                $hasActivity = $intern->user?->dailyActivities ? $intern->user->dailyActivities->filter(function($act) use ($dateStr) {
                    return $act->date instanceof Carbon ? $act->date->format('Y-m-d') === $dateStr : substr($act->date, 0, 10) === $dateStr;
                })->first() : null;

                $status = $hasActivity ? $hasActivity->status : 'none';
                $heatmap[] = [
                    'date' => $targetDate->translatedFormat('d M Y'),
                    'status' => $status
                ];
            }

            $statsPerIntern[] = [
                'user_id' => $intern->user_id,
                'name' => $intern->user?->name ?? 'User',
                'institution' => $intern->institution,
                'total' => $totalSubmitted,
                'approved' => $approvedCount,
                'pending' => $pendingCount,
                'revision' => $revisionCount,
                'approval_rate' => $totalSubmitted > 0 ? round(($approvedCount / $totalSubmitted) * 100) : 0,
                'heatmap' => $heatmap
            ];
        }

        $totalSupervisedCount = $interns->count();
        $avgApprovalRate = $totalReportsAll > 0 ? round(($totalApprovedAll / $totalReportsAll) * 100) : 0;

        return view('supervisor.statistics', compact(
            'statsPerIntern',
            'totalSupervisedCount',
            'avgApprovalRate',
            'totalReportsAll',
            'totalPendingAll',
            'totalRevisionAll'
        ));
    }
}
