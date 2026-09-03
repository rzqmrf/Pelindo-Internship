<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Get badge class and label for Allowance Claim Status
     */
    public static function claimBadge(string $status): array
    {
        return match ($status) {
            'processed_by_hr' => [
                'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                'label' => 'Dicairkan',
                'icon' => 'fa-circle-check',
                'step' => '3/3 Selesai',
            ],
            'approved_by_supervisor' => [
                'class' => 'bg-blue-50 text-blue-600 border-blue-100',
                'label' => 'Disetujui Pembimbing',
                'icon' => 'fa-clock',
                'step' => '2/3 Siap Cair',
            ],
            'rejected' => [
                'class' => 'bg-rose-50 text-rose-600 border-rose-100',
                'label' => 'Ditolak',
                'icon' => 'fa-circle-xmark',
                'step' => 'Ditolak',
            ],
            default => [
                'class' => 'bg-amber-50 text-amber-600 border-amber-100',
                'label' => 'Menunggu Pembimbing',
                'icon' => 'fa-user-clock',
                'step' => '1/3 Spv Review',
            ],
        };
    }

    /**
     * Get badge class and label for Attendance Status
     */
    public static function attendanceBadge(string $status): array
    {
        return match ($status) {
            'present' => [
                'class' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                'label' => 'Tepat Waktu',
                'icon' => 'fa-check',
            ],
            'late' => [
                'class' => 'bg-amber-50 text-amber-600 border-amber-200',
                'label' => 'Terlambat',
                'icon' => 'fa-clock-rotate-left',
            ],
            'leave' => [
                'class' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                'label' => 'Izin / Sakit',
                'icon' => 'fa-calendar-minus',
            ],
            default => [
                'class' => 'bg-rose-50 text-rose-600 border-rose-200',
                'label' => 'Belum Presensi',
                'icon' => 'fa-circle-exclamation',
            ],
        };
    /**
     * Get badge class and label for Work Location (WFO, WFH, WFA, Leave)
     */
    public static function locationBadge(string $location): array
    {
        return match ($location) {
            'WFO' => [
                'class' => 'bg-blue-50 text-blue-700 border-blue-200',
                'label' => 'WFO (Kantor)',
                'icon' => 'fa-building',
            ],
            'WFH' => [
                'class' => 'bg-purple-50 text-purple-700 border-purple-200',
                'label' => 'WFH (Remote)',
                'icon' => 'fa-laptop-house',
            ],
            'WFA' => [
                'class' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                'label' => 'WFA (Tugas Luar)',
                'icon' => 'fa-map-location-dot',
            ],
            default => [
                'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                'label' => 'Izin / Sakit',
                'icon' => 'fa-calendar-xmark',
            ],
        };
    }
}
