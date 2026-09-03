<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DailyActivity;
use App\Models\InternProfile;
use App\Models\User;
use App\Models\Division;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    // ==========================================
    // INTERN ATTENDANCE
    // ==========================================

    public function internIndex(Request $request)
    {
        Carbon::setLocale('id');
        $userId = Auth::id();
        $today = Carbon::today();

        $todayAttendance = Attendance::where('intern_id', $userId)
            ->whereDate('date', $today)
            ->first();

        // Riwayat Bulan Ini
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($selectedMonth . '-01')->endOfMonth();

        $attendances = Attendance::where('intern_id', $userId)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get();

        // Statistics
        $totalPresent = Attendance::where('intern_id', $userId)->whereIn('status', ['present', 'late'])->count();
        $totalWfo = Attendance::where('intern_id', $userId)->where('work_location', 'WFO')->count();
        $totalWfh = Attendance::where('intern_id', $userId)->where('work_location', 'WFH')->count();
        $totalLate = Attendance::where('intern_id', $userId)->where('status', 'late')->count();

        $dailyQrToken = $this->attendanceService->getDailyQrToken();

        return view('intern.attendance.index', compact(
            'todayAttendance',
            'attendances',
            'selectedMonth',
            'totalPresent',
            'totalWfo',
            'totalWfh',
            'totalLate',
            'dailyQrToken'
        ));
    }

    public function checkIn(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $now = Carbon::now();

        // Cek apakah sudah check-in hari ini
        $existing = Attendance::where('intern_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->check_in_time) {
            return back()->with('error', 'Anda sudah melakukan check-in untuk hari ini.');
        }

        $request->validate([
            'work_location' => 'required|in:WFO,WFH,WFA',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'check_in_method' => 'nullable|in:geofence,qr_code',
            'qr_token' => 'nullable|string',
        ]);

        $checkInMethod = $request->input('check_in_method', 'geofence');
        $qrTokenVerified = null;

        // Validasi QR Token jika metode yang dipilih adalah QR Code
        if ($checkInMethod === 'qr_code') {
            $expectedToken = $this->attendanceService->getDailyQrToken($today);
            $inputToken = strtoupper(trim($request->qr_token));

            if ($inputToken !== $expectedToken) {
                return back()->with('error', "Kode QR / Token Presensi Kantor tidak valid. Token hari ini: {$expectedToken}");
            }
            $qrTokenVerified = $inputToken;
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $distance = null;

        if ($lat && $lng) {
            $distance = $this->attendanceService->calculateDistance((float)$lat, (float)$lng);
        }

        // Tentukan status terlambat jika check in setelah 08:30
        $status = 'present';
        $lateCutoff = Carbon::today()->setTime(8, 30, 0);
        if ($now->gt($lateCutoff)) {
            $status = 'late';
        }

        $attendance = Attendance::updateOrCreate(
            [
                'intern_id' => $userId,
                'date' => $today->format('Y-m-d'),
            ],
            [
                'work_location' => $request->work_location,
                'check_in_method' => $checkInMethod,
                'qr_token_verified' => $qrTokenVerified,
                'check_in_time' => $now->format('H:i:s'),
                'check_in_lat' => $lat,
                'check_in_lng' => $lng,
                'check_in_distance_meters' => $distance,
                'check_in_address' => $request->address ?? ($checkInMethod === 'qr_code' ? 'Gedung Pelindo Solusi Digital (Scan QR)' : null),
                'status' => $status,
                'notes' => $request->notes,
            ]
        );

        // Auto-sinkronisasi atau inisiasi draft logbook harian jika belum ada
        $existingActivity = DailyActivity::where('intern_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if (!$existingActivity) {
            $internProfile = InternProfile::where('user_id', $userId)->first();
            DailyActivity::create([
                'intern_id' => $userId,
                'supervisor_id' => $internProfile?->supervisor_id,
                'date' => $today->format('Y-m-d'),
                'work_location' => $request->work_location,
                'activity_description' => '',
                'status' => 'draft',
            ]);
        }

        $msg = "Check-in berhasil tercatat via " . ($checkInMethod === 'qr_code' ? 'Scan QR Code Kantor' : 'Geofencing GPS') . " pada pukul " . $now->format('H:i:s') . " ({$request->work_location}).";
        if ($status === 'late') {
            $msg .= " Status: Terlambat (lewat 08:30 WIB).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Generate dynamic daily QR Token for Office Check-in
     */
    public static function getDailyQrToken($date = null): string
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $hash = strtoupper(substr(md5('PELINDO_HQ_QR_' . $targetDate . '_' . config('app.key', 'pelindo')), 0, 6));
        return 'PEL-' . Carbon::parse($targetDate)->format('Ymd') . '-' . $hash;
    }

    public function checkOut(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('intern_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return back()->with('error', 'Anda belum melakukan check-in hari ini.');
        }

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah melakukan check-out hari ini.');
        }

        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string|max:255',
        ]);

        $attendance->update([
            'check_out_time' => $now->format('H:i:s'),
            'check_out_lat' => $request->latitude,
            'check_out_lng' => $request->longitude,
            'check_out_address' => $request->address,
        ]);

        return back()->with('success', "Check-out berhasil tercatat pada pukul " . $now->format('H:i:s') . ". Terima kasih atas kerja keras Anda hari ini!");
    }

    // ==========================================
    // SUPERVISOR ATTENDANCE MONITORING
    // ==========================================

    public function supervisorIndex(Request $request)
    {
        $supervisorId = Auth::id();
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $interns = User::where('role', 'intern')
            ->whereHas('internProfile', function ($q) use ($supervisorId) {
                $q->where('supervisor_id', $supervisorId);
            })
            ->with(['internProfile.division', 'attendances' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            }])
            ->get();

        return view('supervisor.attendance.index', compact('interns', 'date'));
    }

    // ==========================================
    // ADMIN ATTENDANCE MONITORING
    // ==========================================

    public function adminIndex(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $divisionId = $request->input('division_id');

        $query = Attendance::with(['intern.internProfile.division', 'intern.internProfile.supervisor'])
            ->whereDate('date', $date);

        if ($divisionId) {
            $query->whereHas('intern.internProfile', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $attendances = $query->latest('check_in_time')->paginate(20);
        $divisions = Division::all();

        // Today Summary Counters
        $allActiveInterns = User::where('role', 'intern')->where('is_active', true)->count();
        $totalPresentToday = Attendance::whereDate('date', $date)->whereIn('status', ['present', 'late'])->count();
        $totalWfoToday = Attendance::whereDate('date', $date)->where('work_location', 'WFO')->count();
        $totalWfhToday = Attendance::whereDate('date', $date)->where('work_location', 'WFH')->count();
        $totalLateToday = Attendance::whereDate('date', $date)->where('status', 'late')->count();

        return view('admin.attendance.index', compact(
            'attendances',
            'divisions',
            'date',
            'divisionId',
            'allActiveInterns',
            'totalPresentToday',
            'totalWfoToday',
            'totalWfhToday',
            'totalLateToday'
        ));
    }
}
