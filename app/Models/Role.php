<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Role extends Model
{
    public const CACHE_KEY = 'pelindo_roles_all';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'base_portal',
        'badge_color',
        'permissions',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public static function permissionCatalog(): array
    {
        return [
            'admin.dashboard' => [
                'label' => 'Ringkasan Dashboard Manajemen',
                'description' => 'Melihat KPI magang, grafik tren logbook, dan sebaran peserta per divisi.',
                'route' => 'admin.dashboard',
            ],
            'interns.manage' => [
                'label' => 'Kelola Peserta Magang',
                'description' => 'Menambah, melihat profil detail, mengubah penempatan, dan menghapus peserta magang.',
                'route' => 'admin.interns.index',
            ],
            'supervisors.manage' => [
                'label' => 'Kelola Pembimbing Lapangan',
                'description' => 'Menambah, mengubah, dan mengelola data pembimbing tiap divisi.',
                'route' => 'admin.supervisors.index',
            ],
            'divisions.manage' => [
                'label' => 'Kelola Unit & Kuota Divisi',
                'description' => 'Mengatur daftar divisi, kuota penerimaan peserta, dan nama kepala divisi.',
                'route' => 'admin.divisions.index',
            ],
            'attendance.view' => [
                'label' => 'Rekap Presensi & Geofencing',
                'description' => 'Memantau waktu check-in/out harian, titik koordinat GPS, serta keterlambatan.',
                'route' => 'admin.attendance.index',
            ],
            'monitoring.view' => [
                'label' => 'Monitoring Logbook & Ekspor Rekap',
                'description' => 'Memantau seluruh aktivitas harian peserta lintas divisi dan mengunduh CSV rekap.',
                'route' => 'admin.monitoring',
            ],
            'announcements.manage' => [
                'label' => 'Pengumuman & Siaran Resmi',
                'description' => 'Menerbitkan dan menghapus pengumuman resmi untuk peserta maupun pembimbing.',
                'route' => 'admin.announcements.index',
            ],
            'allowance.manage' => [
                'label' => 'Pencairan Uang Saku & Payroll',
                'description' => 'Mengelola periode klaim, verifikasi pembayaran, bulk-paid, dan ekspor CSV Payroll Bank.',
                'route' => 'admin.allowance.index',
            ],
            'assessments.manage' => [
                'label' => 'Penilaian Akhir & E-Sertifikat',
                'description' => 'Meninjau nilai akhir dari pembimbing dan menerbitkan E-Sertifikat ber-QR Code.',
                'route' => 'admin.assessments.index',
            ],
            'surveys.view' => [
                'label' => 'Rekap Survei Kepuasan Peserta',
                'description' => 'Melihat skor kepuasan rata-rata dan masukan evaluasi dari peserta magang.',
                'route' => 'admin.surveys.index',
            ],
        ];
    }

    public static function portalOptions(): array
    {
        return [
            'admin' => 'Portal Manajemen / Operasional HR',
            'supervisor' => 'Portal Pembimbing Lapangan',
            'intern' => 'Portal Peserta Magang',
            'superadmin' => 'Portal Pengembang Sistem (Superadmin)',
        ];
    }

    public static function badgeOptions(): array
    {
        return [
            'navy' => 'Navy Gelap Pelindo',
            'blue' => 'Biru Utama Pelindo',
            'sky' => 'Biru Muda (Sky)',
            'emerald' => 'Hijau (Emerald)',
            'amber' => 'Kuning (Amber)',
            'rose' => 'Merah Muda (Rose)',
            'slate' => 'Abu-abu Korporat (Slate)',
        ];
    }

    public static function defaultRoles(): array
    {
        $allAdminPermissions = array_keys(self::permissionCatalog());

        return [
            [
                'slug' => 'superadmin',
                'name' => 'Superadmin IT',
                'description' => 'Pengembang dan pemelihara infrastruktur sistem, manajemen role, log, dan kontrol maintenance.',
                'base_portal' => 'superadmin',
                'badge_color' => 'navy',
                'permissions' => ['*'],
                'is_system' => true,
            ],
            [
                'slug' => 'admin',
                'name' => 'Admin HR',
                'description' => 'Pengelola penuh operasional magang, kuota divisi, uang saku, dan penerbitan sertifikat.',
                'base_portal' => 'admin',
                'badge_color' => 'blue',
                'permissions' => $allAdminPermissions,
                'is_system' => true,
            ],
            [
                'slug' => 'supervisor',
                'name' => 'Pembimbing',
                'description' => 'Mentor unit kerja yang memvalidasi logbook harian, izin, klaim uang saku, dan nilai akhir peserta.',
                'base_portal' => 'supervisor',
                'badge_color' => 'sky',
                'permissions' => [],
                'is_system' => true,
            ],
            [
                'slug' => 'intern',
                'name' => 'Peserta Magang',
                'description' => 'Mahasiswa atau siswa peserta magang yang mengisi presensi, logbook, klaim uang saku, dan survei.',
                'base_portal' => 'intern',
                'badge_color' => 'slate',
                'permissions' => [],
                'is_system' => true,
            ],
        ];
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        foreach (self::defaultRoles() as $roleData) {
            static::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        self::flushCache();
    }

    public static function allCached(): Collection
    {
        try {
            if (!Schema::hasTable('roles')) {
                return collect(self::defaultRoles())->map(fn (array $item) => new static($item));
            }

            $rows = Cache::rememberForever(self::CACHE_KEY, function () {
                return static::orderByRaw("CASE slug WHEN 'superadmin' THEN 1 WHEN 'admin' THEN 2 WHEN 'supervisor' THEN 3 WHEN 'intern' THEN 4 ELSE 5 END")
                    ->orderBy('name')
                    ->get();
            });

            if ($rows->isEmpty()) {
                return collect(self::defaultRoles())->map(fn (array $item) => new static($item));
            }

            return $rows;
        } catch (\Throwable $e) {
            return collect(self::defaultRoles())->map(fn (array $item) => new static($item));
        }
    }

    public static function findBySlug(?string $slug): ?self
    {
        if (!$slug) {
            return null;
        }

        return self::allCached()->firstWhere('slug', $slug);
    }

    public static function flushCache(): void
    {
        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable $e) {
            // Ignore when cache table is not ready yet
        }
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->slug === 'superadmin' || $this->base_portal === 'superadmin') {
            return true;
        }

        $perms = is_array($this->permissions) ? $this->permissions : [];

        return in_array('*', $perms, true) || in_array($permission, $perms, true);
    }

    public function badgeClasses(): string
    {
        return match ($this->badge_color) {
            'navy' => 'bg-slate-900 text-white',
            'blue' => 'bg-blue-100 text-[#005B9C]',
            'sky' => 'bg-sky-50 text-sky-800 border border-sky-200',
            'emerald' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
            'amber' => 'bg-amber-50 text-amber-800 border border-amber-200',
            'rose' => 'bg-rose-50 text-rose-800 border border-rose-200',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }
}
