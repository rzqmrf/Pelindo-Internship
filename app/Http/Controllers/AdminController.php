<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\InternProfile;
use App\Models\SupervisorProfile;
use App\Models\DailyActivity;
use App\Models\Attendance;
use App\Models\AllowanceClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Stats Widgets
        $activeInternsCount = InternProfile::where('status', 'active')->count();
        $activeSupervisorsCount = User::where('role', 'supervisor')->where('is_active', true)->count();
        $pendingReportsCount = DailyActivity::where('status', 'pending')->count();
        $completedReportsCount = DailyActivity::where('status', 'approved')->count();

        // 2. Chart 1: Activity Submission Trend (Last X Days)
        $days = intval($request->get('days', 14));
        if (!in_array($days, [7, 14, 30])) {
            $days = 14;
        }

        $trendLabels = [];
        $trendData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trendLabels[] = $date->format('d M');
            $trendData[] = DailyActivity::whereDate('date', $date)->count();
        }

        // 3. Chart 2: Internship Distribution by Division
        $divisions = Division::withCount(['interns' => function ($query) {
            $query->where('status', 'active');
        }])->get();
        $divisionLabels = $divisions->pluck('name')->toArray();
        $divisionData = $divisions->pluck('interns_count')->toArray();

        // 4. Chart 3: Report Status Distribution
        $statusCounts = [
            'Approved' => DailyActivity::where('status', 'approved')->count(),
            'Pending' => DailyActivity::where('status', 'pending')->count(),
            'Revision' => DailyActivity::where('status', 'revision')->count(),
            'Rejected' => DailyActivity::where('status', 'rejected')->count(),
            'Draft' => DailyActivity::where('status', 'draft')->count(),
        ];

        // 5. Recent Activities (Latest 7)
        $recentActivities = DailyActivity::with(['intern', 'supervisor'])
            ->latest()
            ->take(7)
            ->get();

        // 6. Pending Approvals
        $pendingActivities = DailyActivity::with(['intern', 'supervisor'])
            ->where('status', 'pending')
            ->latest()
            ->take(7)
            ->get();

        // 7. Upcoming Internship End (End date within 30 days)
        $upcomingEndingInterns = InternProfile::with(['user', 'division', 'supervisor'])
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->where('end_date', '<=', Carbon::today()->addDays(30))
            ->orderBy('end_date', 'asc')
            ->get();

        // 8. Division Distribution Table Data
        $divisionsData = [];
        foreach (Division::with(['interns.user.dailyActivities', 'supervisors.user'])->get() as $div) {
            $activeInternsCountInDiv = $div->interns->where('status', 'active')->count();
            $supervisorsCount = $div->supervisors->count();
            
            $totalReports = 0;
            $pendingReports = 0;
            $approvedReports = 0;
            
            foreach ($div->interns as $internProfile) {
                if ($internProfile->user) {
                    $reports = $internProfile->user->dailyActivities;
                    $totalReports += $reports->count();
                    $pendingReports += $reports->where('status', 'pending')->count();
                    $approvedReports += $reports->where('status', 'approved')->count();
                }
            }
            
            $progress = $totalReports > 0 ? round(($approvedReports / $totalReports) * 100) : 0;
            
            $divisionsData[] = [
                'name' => $div->name,
                'active_interns_count' => $activeInternsCountInDiv,
                'supervisors_count' => $supervisorsCount,
                'total_reports' => $totalReports,
                'pending_reports' => $pendingReports,
                'progress' => $progress,
            ];
        }

        return view('admin.dashboard', compact(
            'activeInternsCount',
            'activeSupervisorsCount',
            'pendingReportsCount',
            'completedReportsCount',
            'trendLabels',
            'trendData',
            'divisionLabels',
            'divisionData',
            'statusCounts',
            'recentActivities',
            'pendingActivities',
            'upcomingEndingInterns',
            'divisionsData',
            'days'
        ));
    }

    // Kelola Peserta Magang
    public function interns(Request $request)
    {
        $query = InternProfile::with(['user', 'division', 'supervisor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_id_number', 'like', "%{$search}%")
              ->orWhere('institution', 'like', "%{$search}%");
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $interns = $query->latest()->paginate(10);
        $divisions = Division::all();
        $supervisors = User::where('role', 'supervisor')->get();

        $activeInternsCount = InternProfile::where('status', 'active')->count();
        $activeSupervisorsCount = User::where('role', 'supervisor')->where('is_active', true)->count();
        $pendingReportsCount = DailyActivity::where('status', 'pending')->count();
        $completedReportsCount = DailyActivity::where('status', 'approved')->count();

        return view('admin.interns.index', compact(
            'interns', 
            'divisions', 
            'supervisors',
            'activeInternsCount',
            'activeSupervisorsCount',
            'pendingReportsCount',
            'completedReportsCount'
        ));
    }

    public function createIntern()
    {
        $divisions = Division::all();
        $supervisors = User::where('role', 'supervisor')->where('is_active', true)->get();
        return view('admin.interns.create', compact('divisions', 'supervisors'));
    }

    public function showIntern($id)
    {
        $intern = InternProfile::with(['user', 'division', 'supervisor'])->findOrFail($id);
        $user = $intern->user;
        $attendances = Attendance::where('intern_id', $user->id)->latest()->take(10)->get();
        $activities = DailyActivity::where('intern_id', $user->id)->latest()->take(10)->get();
        $claims = AllowanceClaim::where('intern_id', $user->id)->latest()->get();
        $certificate = \App\Models\Certificate::where('intern_id', $user->id)->first();

        return view('admin.interns.show', compact('intern', 'user', 'attendances', 'activities', 'claims', 'certificate'));
    }

    public function editIntern($id)
    {
        $intern = InternProfile::with(['user', 'division', 'supervisor'])->findOrFail($id);
        $divisions = Division::all();
        $supervisors = User::where('role', 'supervisor')->where('is_active', true)->get();
        return view('admin.interns.edit', compact('intern', 'divisions', 'supervisors'));
    }

    public function storeIntern(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'division_id' => 'required|exists:divisions,id',
            'supervisor_id' => 'required|exists:users,id',
            'student_id_number' => 'required|string|max:50',
            'nik_number' => 'nullable|string|max:30',
            'institution' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'religion' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'password' => 'required|string|min:6',
        ]);

        // Check Division Quota
        $division = Division::withCount(['interns' => function ($q) {
            $q->where('status', 'active');
        }])->find($request->division_id);

        if ($division && $division->interns_count >= $division->quota) {
            return redirect()->back()->withInput()->withErrors([
                'division_id' => "Kuota divisi {$division->name} sudah penuh (Maksimal {$division->quota} peserta)."
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'intern',
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        InternProfile::create([
            'user_id' => $user->id,
            'division_id' => $request->division_id,
            'supervisor_id' => $request->supervisor_id,
            'student_id_number' => $request->student_id_number,
            'nik_number' => $request->nik_number,
            'institution' => $request->institution,
            'major' => $request->major,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'address' => $request->address,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_holder' => $request->bank_account_holder,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
        ]);

        return redirect()->route('admin.interns.index')->with('success', 'Peserta magang baru berhasil ditambahkan.');
    }

    public function updateIntern(Request $request, $id)
    {
        $intern = InternProfile::findOrFail($id);
        $user = $intern->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:20',
            'division_id' => 'required|exists:divisions,id',
            'supervisor_id' => 'required|exists:users,id',
            'student_id_number' => 'required|string|max:50',
            'nik_number' => 'nullable|string|max:30',
            'institution' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'religion' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,completed,extended,terminated',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $user->update($userData);

        $intern->update([
            'division_id' => $request->division_id,
            'supervisor_id' => $request->supervisor_id,
            'student_id_number' => $request->student_id_number,
            'nik_number' => $request->nik_number,
            'institution' => $request->institution,
            'major' => $request->major,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'address' => $request->address,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_holder' => $request->bank_account_holder,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.interns.index')->with('success', 'Data peserta magang berhasil diperbarui.');
    }

    public function deleteIntern($id)
    {
        $intern = InternProfile::findOrFail($id);
        $user = $intern->user;
        if ($user) {
            $user->delete(); // Cascades intern_profile and daily_activities
        } else {
            $intern->delete();
        }

        return redirect()->back()->with('success', 'Peserta magang berhasil dihapus.');
    }

    // Kelola Pembimbing
    public function supervisors(Request $request)
    {
        $query = SupervisorProfile::with(['user', 'division']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            })->orWhere('employee_id_number', 'like', "%{$search}%");
        }

        $supervisors = $query->latest()->paginate(10);
        $divisions = Division::all();

        // Calculate KPI metrics
        $totalSupervisors = User::where('role', 'supervisor')->where('is_active', true)->count();
        $coveredDivisionsCount = SupervisorProfile::whereNotNull('division_id')->distinct('division_id')->count('division_id');
        $totalActiveInterns = InternProfile::where('status', 'active')->count();
        $avgInternsPerSpv = $totalSupervisors > 0 ? round($totalActiveInterns / $totalSupervisors) : 0;
        $oldestJoiningDate = SupervisorProfile::min('created_at');
        $oldestJoiningLabel = $oldestJoiningDate ? \Carbon\Carbon::parse($oldestJoiningDate)->isoFormat('MMM YYYY') : 'Jan 2024';

        return view('admin.supervisors.index', compact(
            'supervisors', 
            'divisions',
            'totalSupervisors',
            'coveredDivisionsCount',
            'avgInternsPerSpv',
            'oldestJoiningLabel'
        ));
    }

    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'division_id' => 'required|exists:divisions,id',
            'employee_id_number' => 'required|string|max:50',
            'position_title' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'supervisor',
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        SupervisorProfile::create([
            'user_id' => $user->id,
            'division_id' => $request->division_id,
            'employee_id_number' => $request->employee_id_number,
            'position_title' => $request->position_title,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Pembimbing baru berhasil ditambahkan.');
    }

    public function updateSupervisor(Request $request, $id)
    {
        $spvProfile = SupervisorProfile::findOrFail($id);
        $user = $spvProfile->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:20',
            'division_id' => 'required|exists:divisions,id',
            'employee_id_number' => 'required|string|max:50',
            'position_title' => 'required|string|max:255',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $user->update($userData);

        $spvProfile->update([
            'division_id' => $request->division_id,
            'employee_id_number' => $request->employee_id_number,
            'position_title' => $request->position_title,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Data pembimbing berhasil diperbarui.');
    }

    public function deleteSupervisor($id)
    {
        $spvProfile = SupervisorProfile::findOrFail($id);
        $user = $spvProfile->user;
        if ($user) {
            $user->delete();
        } else {
            $spvProfile->delete();
        }

        return redirect()->back()->with('success', 'Pembimbing berhasil dihapus.');
    }

    // Monitoring Seluruh Peserta & Aktivitas
    public function monitoring(Request $request)
    {
        $query = DailyActivity::with(['intern.internProfile.division', 'supervisor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('intern', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('activity_description', 'like', "%{$search}%");
        }

        if ($request->filled('division_id')) {
            $query->whereHas('intern.internProfile', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('work_location')) {
            $query->where('work_location', $request->work_location);
        }

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('date', [$request->date_start, $request->date_end]);
        }

        $activities = $query->latest('date')->paginate(15);
        $divisions = Division::all();
        $supervisors = User::where('role', 'supervisor')->get();
        $interns = User::where('role', 'intern')->get();

        // Calculate KPI stats
        $activitiesTodayCount = DailyActivity::whereDate('date', Carbon::today())->count();
        $activitiesYesterdayCount = DailyActivity::whereDate('date', Carbon::yesterday())->count();
        $pctDiff = $activitiesYesterdayCount > 0 ? round((($activitiesTodayCount - $activitiesYesterdayCount) / $activitiesYesterdayCount) * 100) : 16;
        
        $totalActivities = DailyActivity::count();
        $approvedCount = DailyActivity::where('status', 'approved')->count();
        $pendingCount = DailyActivity::where('status', 'pending')->count();
        $revisionCount = DailyActivity::where('status', 'revision')->count();
        $rejectedCount = DailyActivity::where('status', 'rejected')->count();

        $pctApproved = $totalActivities > 0 ? round(($approvedCount / $totalActivities) * 100, 1) : 0;
        $pctPending = $totalActivities > 0 ? round(($pendingCount / $totalActivities) * 100, 1) : 0;
        $pctRevision = $totalActivities > 0 ? round(($revisionCount / $totalActivities) * 100, 1) : 0;
        $pctRejected = $totalActivities > 0 ? round(($rejectedCount / $totalActivities) * 100, 1) : 0;

        return view('admin.monitoring', compact(
            'activities', 
            'divisions', 
            'supervisors', 
            'interns',
            'activitiesTodayCount',
            'pctDiff',
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

    /**
     * Export attendance summary by division and month for HR Admin.
     */
    public function exportDivisionRekap(Request $request)
    {
        $request->validate([
            'division_id' => 'nullable|exists:divisions,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $monthStr = $request->month; // format 'Y-m'
        $parsedMonth = Carbon::parse($monthStr . '-01');
        $divisionId = $request->division_id;

        $query = InternProfile::with(['user.dailyActivities', 'division', 'supervisor'])
            ->where('status', 'active');

        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        $interns = $query->get();

        $divisionName = $divisionId 
            ? str_replace('Divisi ', '', Division::find($divisionId)->name) 
            : 'Semua_Divisi';

        $fileName = 'Rekap_Presensi_Magang_' . $divisionName . '_' . $monthStr . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($interns, $parsedMonth) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($file, [
                'No',
                'Nama Peserta magang',
                'NIM/NIS',
                'Instansi Pendidikan',
                'Divisi / Unit Kerja',
                'Pembimbing Lapangan',
                'Bulan Rekap',
                'Hadir (WFO)',
                'Hadir (WFH)',
                'Izin / Sakit (Leave)',
                'Total Hadir',
                'Total Hari Kerja Terisi',
            ]);

            $no = 1;
            foreach ($interns as $intern) {
                $wfoCount = 0;
                $wfhCount = 0;
                $leaveCount = 0;
                $totalLogbookFilled = 0;

                if ($intern->user) {
                    $activities = $intern->user->dailyActivities()
                        ->whereYear('date', $parsedMonth->year)
                        ->whereMonth('date', $parsedMonth->month)
                        ->get();

                    $totalLogbookFilled = $activities->count();
                    $wfoCount = $activities->where('work_location', 'WFO')->count();
                    $wfhCount = $activities->where('work_location', 'WFH')->count();
                    $leaveCount = $activities->where('work_location', 'Leave')->count();
                }

                $totalPresence = $wfoCount + $wfhCount + $leaveCount;

                fputcsv($file, [
                    $no++,
                    $intern->user?->name ?? '-',
                    $intern->student_id_number ?? '-',
                    $intern->institution ?? '-',
                    $intern->division?->name ?? '-',
                    $intern->supervisor?->name ?? '-',
                    $parsedMonth->isoFormat('MMMM YYYY'),
                    $wfoCount,
                    $wfhCount,
                    $leaveCount,
                    $totalPresence,
                    $totalLogbookFilled,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
