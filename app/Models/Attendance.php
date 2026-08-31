<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_id',
        'date',
        'work_location',
        'check_in_time',
        'check_in_lat',
        'check_in_lng',
        'check_in_distance_meters',
        'check_in_address',
        'check_out_time',
        'check_out_lat',
        'check_out_lng',
        'check_out_address',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_lat' => 'float',
        'check_in_lng' => 'float',
        'check_out_lat' => 'float',
        'check_out_lng' => 'float',
        'check_in_distance_meters' => 'integer',
    ];

    public function intern()
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    /**
     * Kantor Pusat Regional Pelindo Surabaya coordinates
     */
    const OFFICE_LAT = -7.2023000;
    const OFFICE_LNG = 112.7315000;
    const MAX_ALLOWED_DISTANCE_METERS = 500; // 500m geofence radius

    /**
     * Calculate distance between coordinates using Haversine formula
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2 = self::OFFICE_LAT, float $lon2 = self::OFFICE_LNG): int
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
}
