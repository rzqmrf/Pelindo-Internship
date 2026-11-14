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
        $corrections = \App\Models\AttendanceCorrection::where('intern_id', $userId)
            ->latest('date')
            ->take(10)
            ->get();

        return view('intern.attendance.index', compact(
            'todayAttendance',
            'attendances',
            'selectedMonth',
            'totalPresent',
            'totalWfo',
            'totalWfh',
            'totalLate',
            'dailyQrToken',
            'corrections'
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
            $inputToken = strtoupper(trim((string) $request->qr_token));

            if ($inputToken === '' || $inputToken !== $expectedToken) {
                return back()->with('error', 'Kode QR / Token Presensi Kantor tidak sesuai atau sudah kedaluwarsa. Silakan pindai ulang QR Code resmi di layar resepsionis kantor.');
            }
            $qrTokenVerified = $inputToken;
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $distance = null;
        $maxAllowedMeters = $this->attendanceService->getMaxAllowedDistance();

        if ($lat !== null && $lng !== null && $lat !== '' && $lng !== '') {
            $distance = $this->attendanceService->calculateDistance((float) $lat, (float) $lng);
        }

        if ($request->work_location === 'WFO' && $checkInMethod === 'geofence') {
            if ($distance === null) {
                return back()->with('error', 'Koordinat GPS belum terdeteksi untuk presensi WFO. Aktifkan izin lokasi browser atau gunakan metode Scan QR Kantor.');
            }
            if ($distance > $maxAllowedMeters) {
                return back()->with('error', "Lokasi Anda berjarak {$distance} meter dari titik kantor (batas toleransi WFO: {$maxAllowedMeters} meter). Gunakan Scan QR Kantor jika berada di dalam gedung.");
            }
        }

        // Tentukan status terlambat berdasarkan jam batas sistem
        $status = 'present';
        $cutoffStr = $this->attendanceService->getLateCutoffTime();
        [$cutHour, $cutMin] = array_map('intval', explode(':', $cutoffStr));
        $lateCutoff = Carbon::today()->setTime($cutHour, $cutMin, 0);
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
            $msg .= " Status: Terlambat (lewat {$cutoffStr} WIB).";
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

    public function requestCorrection(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'correction_type' => 'required|in:missed_checkin,missed_checkout,gps_issue,both',
            'requested_check_in' => 'nullable|date_format:H:i',
            'requested_check_out' => 'nullable|date_format:H:i',
            'work_location' => 'required|in:WFO,WFH,WFA',
            'reason' => 'required|string|min:10|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attendance_corrections', 'public');
        }

        $profile = Auth::user()->internProfile;
        $correction = \App\Models\AttendanceCorrection::create([
            'intern_id' => Auth::id(),
            'supervisor_id' => $profile?->supervisor_id,
            'date' => $request->date,
            'correction_type' => $request->correction_type,
            'requested_check_in' => $request->requested_check_in,
            'requested_check_out' => $request->requested_check_out,
            'work_location' => $request->work_location,
            'reason' => $request->reason,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        if ($profile?->supervisor_id) {
            \App\Models\Notification::create([
                'user_id' => $profile->supervisor_id,
                'title' => 'Pengajuan Koreksi Presensi',
                'message' => Auth::user()->name . ' mengajukan koreksi presensi untuk tanggal ' . Carbon::parse($request->date)->isoFormat('D MMMM YYYY') . '.',
            ]);
        }

        return back()->with('success', 'Pengajuan koreksi presensi berhasil dikirim dan menunggu persetujuan pembimbing.');
    }

    // ==========================================
    // SUPERVISOR ATTENDANCE MONITORING & CORRECTIONS
    // ==========================================

    public function supervisorIndex(Request $request)
    {
        $supervisorIds = Auth::user()->accessibleSupervisorIds();
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $interns = User::where('role', 'intern')
            ->whereHas('internProfile', function ($q) use ($supervisorIds) {
                $q->whereIn('supervisor_id', $supervisorIds);
            })
            ->with(['internProfile.division', 'attendances' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            }])
            ->get();

        $pendingCorrections = \App\Models\AttendanceCorrection::with('intern.internProfile.division')
            ->whereIn('supervisor_id', $supervisorIds)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('supervisor.attendance.index', compact('interns', 'date', 'pendingCorrections'));
    }

    public function approveCorrection(Request $request, $id)
    {
        $supervisorIds = Auth::user()->accessibleSupervisorIds();
        $correction = \App\Models\AttendanceCorrection::where('id', $id)
            ->whereIn('supervisor_id', $supervisorIds)
            ->firstOrFail();

        $this->applyCorrectionApproval($correction, $request->reviewer_note);

        return back()->with('success', 'Koreksi presensi disetujui dan data presensi telah diperbarui.');
    }

    public function rejectCorrection(Request $request, $id)
    {
        $supervisorIds = Auth::user()->accessibleSupervisorIds();
        $correction = \App\Models\AttendanceCorrection::where('id', $id)
            ->whereIn('supervisor_id', $supervisorIds)
            ->firstOrFail();

        $correction->update([
            'status' => 'rejected',
            'reviewer_id' => Auth::id(),
            'reviewer_note' => $request->reviewer_note,
            'reviewed_at' => Carbon::now(),
        ]);

        \App\Models\Notification::create([
            'user_id' => $correction->intern_id,
            'title' => 'Koreksi Presensi Ditolak',
            'message' => 'Pengajuan koreksi presensi tanggal ' . Carbon::parse($correction->date)->isoFormat('D MMMM YYYY') . ' ditolak.',
        ]);

        return back()->with('success', 'Pengajuan koreksi presensi ditolak.');
    }

    // ==========================================
    // ADMIN ATTENDANCE MONITORING, EXPORT & OVERRIDE
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

        $attendances = $query->latest('check_in_time')->paginate(20)->withQueryString();
        $divisions = Division::all();

        // Today Summary Counters
        $allActiveInterns = User::where('role', 'intern')->where('is_active', true)->count();
        $totalPresentToday = Attendance::whereDate('date', $date)->whereIn('status', ['present', 'late'])->count();
        $totalWfoToday = Attendance::whereDate('date', $date)->where('work_location', 'WFO')->count();
        $totalWfhToday = Attendance::whereDate('date', $date)->where('work_location', 'WFH')->count();
        $totalLateToday = Attendance::whereDate('date', $date)->where('status', 'late')->count();

        $pendingCorrections = \App\Models\AttendanceCorrection::with(['intern.internProfile.division', 'supervisor'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.attendance.index', compact(
            'attendances',
            'divisions',
            'date',
            'divisionId',
            'allActiveInterns',
            'totalPresentToday',
            'totalWfoToday',
            'totalWfhToday',
            'totalLateToday',
            'pendingCorrections'
        ));
    }

    public function exportMonthlyExcel(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $divisionId = $request->input('division_id');
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();

        $query = Attendance::with(['intern.internProfile.division', 'intern.internProfile.supervisor'])
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')]);

        if ($divisionId) {
            $query->whereHas('intern.internProfile', function ($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $rows = $query->orderBy('date')->orderBy('check_in_time')->get();
        $filename = "rekap_presensi_{$month}.csv";

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Tanggal',
                'Nama Peserta',
                'Email',
                'Divisi',
                'Pembimbing',
                'Lokasi Kerja',
                'Check-In',
                'Check-Out',
                'Status',
                'Metode',
                'Jarak (m)',
            ]);

            foreach ($rows as $att) {
                fputcsv($handle, [
                    $att->date?->format('Y-m-d') ?? '-',
                    $att->intern?->name ?? '-',
                    $att->intern?->email ?? '-',
                    $att->intern?->internProfile?->division?->name ?? '-',
                    $att->intern?->internProfile?->supervisor?->name ?? '-',
                    $att->work_location,
                    $att->check_in_time ?? '-',
                    $att->check_out_time ?? '-',
                    $att->status,
                    $att->check_in_method ?? 'geofence',
                    $att->check_in_distance_meters ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function adminApproveCorrection(Request $request, $id)
    {
        $correction = \App\Models\AttendanceCorrection::findOrFail($id);
        $this->applyCorrectionApproval($correction, $request->reviewer_note ?: 'Disetujui oleh Admin HR.');

        return back()->with('success', 'Koreksi presensi berhasil disetujui oleh Admin HR.');
    }

    public function adminRejectCorrection(Request $request, $id)
    {
        $correction = \App\Models\AttendanceCorrection::findOrFail($id);
        $correction->update([
            'status' => 'rejected',
            'reviewer_id' => Auth::id(),
            'reviewer_note' => $request->reviewer_note ?: 'Ditolak oleh Admin HR.',
            'reviewed_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Koreksi presensi ditolak oleh Admin HR.');
    }

    private function applyCorrectionApproval(\App\Models\AttendanceCorrection $correction, ?string $note): void
    {
        $dateStr = $correction->date instanceof Carbon
            ? $correction->date->format('Y-m-d')
            : Carbon::parse($correction->date)->format('Y-m-d');

        $attendance = Attendance::firstOrNew([
            'intern_id' => $correction->intern_id,
            'date' => $dateStr,
        ]);

        $attendance->work_location = $correction->work_location;
        if ($correction->requested_check_in) {
            $attendance->check_in_time = $correction->requested_check_in . ':00';
        }
        if ($correction->requested_check_out) {
            $attendance->check_out_time = $correction->requested_check_out . ':00';
        }
        if (empty($attendance->status)) {
            $attendance->status = 'present';
        }
        $attendance->notes = trim(($attendance->notes ? $attendance->notes . ' | ' : '') . 'Dikoreksi: ' . $correction->reason);
        $attendance->save();

        $correction->update([
            'status' => 'approved',
            'reviewer_id' => Auth::id(),
            'reviewer_note' => $note,
            'reviewed_at' => Carbon::now(),
        ]);

        \App\Models\Notification::create([
            'user_id' => $correction->intern_id,
            'title' => 'Koreksi Presensi Disetujui',
            'message' => 'Pengajuan koreksi presensi tanggal ' . Carbon::parse($dateStr)->isoFormat('D MMMM YYYY') . ' telah disetujui.',
        ]);
    }
}
