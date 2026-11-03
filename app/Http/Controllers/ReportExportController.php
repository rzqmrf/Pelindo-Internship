<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\DailyActivity;
use App\Models\InternProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportExportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $divisions = Division::all();
        $supervisors = User::where('role', 'supervisor')->get();
        $interns = User::where('role', 'intern')->with('internProfile')->get();

        return view('reports.index', compact('divisions', 'supervisors', 'interns', 'user'));
    }

    public function generatePdf(Request $request)
    {
        Carbon::setLocale('id');

        $user = Auth::user();
        if ($user->isIntern()) {
            if ($request->filled('intern_id') && (int) $request->intern_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengunduh laporan magang milik Anda sendiri.');
            }
            $internId = $user->id;
        } else {
            $internId = $request->intern_id;
        }

        $internUser = User::with(['internProfile.division', 'internProfile.supervisor.supervisorProfile'])->findOrFail($internId);
        $internProfile = $internUser->internProfile;

        $isMonthly = $request->filled('month');
        if ($isMonthly) {
            $parsedMonth = Carbon::parse($request->month . '-01');
            $startDate = $parsedMonth->copy()->startOfMonth();
            $endDate = $parsedMonth->copy()->endOfMonth();
        } else {
            // Determine Start Date
            $startDate = $request->filled('start_date')
                ? Carbon::parse($request->start_date)
                : ($internProfile?->start_date ?? Carbon::now()->startOfMonth());

            // Calculate the end date which is the 20th weekday starting from $startDate
            $endDate = $startDate->copy();
            $weekdayCount = 0;
            while ($weekdayCount < 20) {
                if (!$endDate->isWeekend()) {
                    $weekdayCount++;
                }
                if ($weekdayCount < 20) {
                    $endDate->addDay();
                }
            }
        }

        // Get activities in this range
        $query = DailyActivity::where('intern_id', $internId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->orderBy('date', 'asc')->get()->keyBy(function ($act) {
            return Carbon::parse($act->date)->format('Y-m-d');
        });

        // Generate list of weekdays
        $reportRows = [];
        $currentDate = $startDate->copy();

        if ($isMonthly) {
            while ($currentDate->lte($endDate)) {
                if (!$currentDate->isWeekend()) {
                    $dateStr = $currentDate->format('Y-m-d');
                    if (isset($activities[$dateStr])) {
                        $act = $activities[$dateStr];
                        $reportRows[] = [
                            'date' => $currentDate->copy(),
                            'activity_description' => $act->activity_description,
                            'work_location' => $act->work_location,
                            'notes' => $act->notes,
                            'status' => $act->status,
                            'supervisor_note' => $act->supervisor_note,
                        ];
                    } else {
                        // Default work location: WFH on Fridays, WFO on other weekdays
                        $defaultLoc = $currentDate->isFriday() ? 'WFH' : 'WFO';
                        $reportRows[] = [
                            'date' => $currentDate->copy(),
                            'activity_description' => '',
                            'work_location' => $defaultLoc,
                            'notes' => '',
                            'status' => '',
                            'supervisor_note' => '',
                        ];
                    }
                }
                $currentDate->addDay();
            }
            $periodLabel = $startDate->isoFormat('MMMM YYYY');
        } else {
            $weekdayCount = 0;
            while ($weekdayCount < 20) {
                if (!$currentDate->isWeekend()) {
                    $dateStr = $currentDate->format('Y-m-d');
                    if (isset($activities[$dateStr])) {
                        $act = $activities[$dateStr];
                        $reportRows[] = [
                            'date' => $currentDate->copy(),
                            'activity_description' => $act->activity_description,
                            'work_location' => $act->work_location,
                            'notes' => $act->notes,
                            'status' => $act->status,
                            'supervisor_note' => $act->supervisor_note,
                        ];
                    } else {
                        // Default work location: WFH on Fridays, WFO on other weekdays
                        $defaultLoc = $currentDate->isFriday() ? 'WFH' : 'WFO';
                        $reportRows[] = [
                            'date' => $currentDate->copy(),
                            'activity_description' => '',
                            'work_location' => $defaultLoc,
                            'notes' => '',
                            'status' => '',
                            'supervisor_note' => '',
                        ];
                    }
                    $weekdayCount++;
                }
                $currentDate->addDay();
            }
            $periodLabel = $startDate->isoFormat('D MMMM') . ' - ' . $endDate->isoFormat('D MMMM YYYY');
        }

        // Dynamic signature date (end of report, or today if report end date is in the future)
        $sigDate = $endDate->gt(Carbon::now()) ? Carbon::now() : $endDate;
        $generatedDate = $sigDate->isoFormat('D MMMM YYYY');

        $data = [
            'internUser' => $internUser,
            'internProfile' => $internProfile,
            'reportRows' => $reportRows,
            'periodLabel' => $periodLabel,
            'generatedDate' => $generatedDate,
            'companyName' => 'PT PELABUHAN INDONESIA (PERSERO)',
        ];

        $pdf = Pdf::loadView('reports.pelindo_pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'Laporan_Magang_Pelindo_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $internUser->name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $query = DailyActivity::with(['intern.internProfile.division', 'supervisor']);

        if ($user->isIntern()) {
            if ($request->filled('intern_id') && (int) $request->intern_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengekspor laporan milik Anda sendiri.');
            }
            $query->where('intern_id', $user->id);
        } elseif ($request->filled('intern_id')) {
            $query->where('intern_id', $request->intern_id);
        }

        if ($request->filled('division_id')) {
            $query->whereHas('intern.internProfile', function ($q) use ($request) {
                $q->where('division_id', $request->division_id);
            });
        }

        if ($request->filled('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $activities = $query->orderBy('date', 'asc')->get();

        $fileName = 'Rekap_Aktivitas_Magang_Pelindo_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($activities) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($file, [
                'No',
                'Nama Peserta',
                'NIM/NIS',
                'Instansi',
                'Divisi',
                'Pembimbing',
                'Tanggal',
                'Lokasi Kerja',
                'Deskripsi Kegiatan',
                'Catatan / Results',
                'Status Approval',
                'Catatan Pembimbing'
            ]);

            $no = 1;
            foreach ($activities as $act) {
                $profile = $act->intern?->internProfile;
                fputcsv($file, [
                    $no++,
                    $act->intern?->name ?? '-',
                    $profile?->student_id_number ?? '-',
                    $profile?->institution ?? '-',
                    $profile?->division?->name ?? '-',
                    $act->supervisor?->name ?? '-',
                    $act->date ? $act->date->format('Y-m-d') : '-',
                    $act->work_location,
                    $act->activity_description,
                    $act->notes ?? '',
                    strtoupper($act->status),
                    $act->supervisor_note ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
