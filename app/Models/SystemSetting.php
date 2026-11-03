<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SystemSetting extends Model
{
    public const CACHE_KEY = 'pelindo_system_settings_map';

    protected $fillable = [
        'key',
        'value',
        'type',
        'updated_by',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function defaults(): array
    {
        return [
            'maintenance_mode' => '0',
            'maintenance_title' => 'Pemeliharaan Sistem Terjadwal',
            'maintenance_message' => 'Kami sedang melakukan pembaruan infrastruktur dan peningkatan keamanan pada Pelindo Internship Portal. Silakan akses kembali beberapa saat lagi.',
            'maintenance_eta' => '',
            'maintenance_contact' => 'it.helpdesk@pelindo.co.id',
            'maintenance_allow_cert_verify' => '1',
            'maintenance_bypass_token' => '',
            'banner_enabled' => '0',
            'banner_level' => 'info', // info | warning | critical
            'banner_title' => '',
            'banner_message' => '',
            'banner_schedule_at' => '',
            'office_lat' => '-7.2023000',
            'office_lng' => '112.7315000',
            'max_geofence_meters' => '500',
            'late_cutoff_time' => '08:30',
            'allowance_wfo_rate' => '71250',
            'allowance_wfh_rate' => '71250',
            'allowance_wfa_rate' => '71250',
        ];
    }

    public static function allCached(): array
    {
        try {
            if (!Schema::hasTable('system_settings')) {
                return static::defaults();
            }

            return Cache::rememberForever(self::CACHE_KEY, function () {
                $stored = static::query()->pluck('value', 'key')->toArray();

                return array_merge(static::defaults(), $stored);
            });
        } catch (\Throwable $e) {
            return static::defaults();
        }
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $val = static::getValue($key, $default ? '1' : '0');

        return in_array((string) $val, ['1', 'true', 'yes', 'on'], true);
    }

    public static function setMany(array $pairs, ?int $updatedBy = null): void
    {
        foreach ($pairs as $key => $info) {
            $value = is_array($info) ? ($info['value'] ?? null) : $info;
            $type = is_array($info) ? ($info['type'] ?? 'string') : 'string';

            static::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value !== null ? (string) $value : null,
                    'type' => $type,
                    'updated_by' => $updatedBy,
                ]
            );
        }

        static::flushCache();
    }

    public static function flushCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable $e) {
            // Ignore when cache table is unavailable during fresh boot
        }
    }
}
