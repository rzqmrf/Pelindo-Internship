<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DailyActivity;
use App\Models\Attendance;
use App\Models\AllowanceClaim;
use App\Models\LeaveRequest;
use App\Models\Notification;
use Carbon\Carbon;

class SendInternshipReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'internship:send-reminders {--force : Jalankan tanpa memeriksa hari kerja}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat otomatis logbook, presensi, dan persetujuan pending';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Carbon::setLocale('id');
        $today = Carbon::today();
        $isWeekday = !$today->isWeekend() || $this->option('force');

        $this->info('🚀 Menjalankan scheduler pengingat sistem Pelindo Internship...');

        $internReminderCount = 0;
        $supervisorReminderCount = 0;

        // 1. Pengingat Harian untuk Peserta Magang (Senin - Jumat)
        if ($isWeekday) {
            $activeInterns = User::where('role', 'intern')
                ->where('is_active', true)
                ->whereHas('internProfile', function ($q) {
                    $q->where('status', 'active');
                })
                ->get();

            foreach ($activeInterns as $intern) {
                // Cek apakah sudah mengisi aktivitas hari ini
                $hasActivity = DailyActivity::where('intern_id', $intern->id)
                    ->whereDate('date', $today)
                    ->exists();

                $hasAttendance = Attendance::where('intern_id', $intern->id)
                    ->whereDate('date', $today)
                    ->exists();

                if (!$hasActivity || !$hasAttendance) {
                    // Hindari spam notifikasi ganda pada hari yang sama
                    $alreadyNotified = Notification::where('user_id', $intern->id)
                        ->where('title', 'like', '%Pengingat Harian%')
                        ->whereDate('created_at', $today)
                        ->exists();

                    if (!$alreadyNotified) {
                        Notification::sendToUser(
                            $intern,
                            '⏰ Pengingat Harian: Isi Presensi & Logbook',
                            "Halo {$intern->name}, jangan lupa melakukan presensi dan melengkapi logbook aktivitas magang Anda untuk hari " . $today->isoFormat('dddd, D MMMM YYYY') . '.'
                        );
                        $internReminderCount++;
                    }
                }
            }
        }

        // 2. Pengingat untuk Pembimbing Lapangan jika ada task pending > 24 jam
        $supervisors = User::where('role', 'supervisor')
            ->where('is_active', true)
            ->get();

        foreach ($supervisors as $spv) {
            $pendingActivities = DailyActivity::where('supervisor_id', $spv->id)
                ->where('status', 'pending')
                ->where('created_at', '<=', Carbon::now()->subHours(24))
                ->count();

            $pendingClaims = AllowanceClaim::where('supervisor_id', $spv->id)
                ->where('status', 'pending_supervisor')
                ->count();

            $pendingLeaves = LeaveRequest::where('supervisor_id', $spv->id)
                ->where('status', 'pending')
                ->count();

            $totalPending = $pendingActivities + $pendingClaims + $pendingLeaves;

            if ($totalPending > 0) {
                $alreadyNotifiedSpv = Notification::where('user_id', $spv->id)
                    ->where('title', 'like', '%Persetujuan Menunggu%')
                    ->whereDate('created_at', $today)
                    ->exists();

                if (!$alreadyNotifiedSpv) {
                    Notification::sendToUser(
                        $spv,
                        '📋 Pengingat: Persetujuan Menunggu Review',
                        "Terdapat {$totalPending} pengajuan aktivitas/klaim/izin dari peserta bimbingan Anda yang belum direview."
                    );
                    $supervisorReminderCount++;
                }
            }
        }

        $this->info("✅ Pengingat selesai dikirim: {$internReminderCount} peserta magang, {$supervisorReminderCount} pembimbing.");

        return Command::SUCCESS;
    }
}
