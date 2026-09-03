<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceService
{
    const OFFICE_LAT = -7.2023000;
    const OFFICE_LNG = 112.7315000;
    const MAX_ALLOWED_DISTANCE_METERS = 500;

    /**
     * Calculate distance between coordinates using Haversine formula (in meters)
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2 = self::OFFICE_LAT, float $lon2 = self::OFFICE_LNG): int
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }

    /**
     * Generate dynamic daily QR Token for Office Check-in
     */
    public function getDailyQrToken($date = null): string
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $hash = strtoupper(substr(md5('PELINDO_HQ_QR_' . $targetDate . '_' . config('app.key', 'pelindo')), 0, 6));
        return 'PEL-' . Carbon::parse($targetDate)->format('Ymd') . '-' . $hash;
    }
}
