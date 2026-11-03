<?php

namespace App\Services;

use App\Models\SystemSetting;
use Carbon\Carbon;

class AttendanceService
{
    const OFFICE_LAT = -7.2023000;
    const OFFICE_LNG = 112.7315000;
    const MAX_ALLOWED_DISTANCE_METERS = 500;

    public function getOfficeLat(): float
    {
        return (float) SystemSetting::getValue('office_lat', self::OFFICE_LAT);
    }

    public function getOfficeLng(): float
    {
        return (float) SystemSetting::getValue('office_lng', self::OFFICE_LNG);
    }

    public function getMaxAllowedDistance(): int
    {
        return max(10, (int) SystemSetting::getValue('max_geofence_meters', self::MAX_ALLOWED_DISTANCE_METERS));
    }

    public function getLateCutoffTime(): string
    {
        $val = (string) SystemSetting::getValue('late_cutoff_time', '08:30');

        return preg_match('/^\d{2}:\d{2}$/', $val) ? $val : '08:30';
    }

    /**
     * Calculate distance between coordinates using Haversine formula (in meters)
     */
    public function calculateDistance(float $lat1, float $lon1, ?float $lat2 = null, ?float $lon2 = null): int
    {
        $targetLat = $lat2 ?? $this->getOfficeLat();
        $targetLon = $lon2 ?? $this->getOfficeLng();
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($targetLat - $lat1);
        $lonDelta = deg2rad($targetLon - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($targetLat)) *
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
