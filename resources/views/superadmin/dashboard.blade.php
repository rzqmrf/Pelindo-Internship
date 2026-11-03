@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header Ringkasan Pengembang -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">
                Pusat Kendali Pengembang Sistem
            </h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Pantau kesehatan infrastruktur, kapasitas penyimpanan berkas, cache Laravel, dan status pemeliharaan portal.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="{{ route('superadmin.system.backup_db') }}" class="px-5 py-3 rounded-xl bg-[#002B49] hover:bg-slate-900 text-white font-extrabold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-database"></i>
                <span>Backup Database</span>
            </a>
            <a href="{{ route('superadmin.maintenance') }}" class="px-5 py-3 rounded-xl font-extrabold text-xs transition flex items-center gap-2 {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-[#005B9C] hover:bg-[#002B49] text-white' }}">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span>{{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'Maintenance Aktif (Kelola)' : 'Atur Jadwal Maintenance' }}</span>
            </a>
            <a href="{{ route('superadmin.settings') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-sliders text-[#005B9C]"></i>
                <span>Parameter Sistem</span>
            </a>
        </div>
    </div>

    <!-- Baris Status Infrastruktur Nyata -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Database Health -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Koneksi Database</span>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold {{ $health['db_connected'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    {{ $health['db_connected'] ? 'TERHUBUNG' : 'TERPUTUS' }}
                </span>
            </div>
            <div class="flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black text-[#002B49]">{{ $health['db_driver'] }}</p>
                    <p class="text-xs font-semibold text-slate-600 mt-1">Ukuran data: <strong>{{ $health['db_size_human'] }}</strong></p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-500">Latensi Query</p>
                    <p class="text-sm font-black text-slate-800">{{ $health['db_latency_ms'] !== null ? $health['db_latency_ms'] . ' ms' : 'N/A' }}</p>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-600">
                <a href="{{ route('superadmin.sessions') }}" class="hover:text-[#005B9C]">Sesi Aktif: <strong>{{ $health['active_sessions'] }}</strong></a>
                <a href="{{ route('superadmin.sessions') }}" class="hover:text-[#005B9C]">Antrean Job: <strong>{{ $health['pending_jobs'] }}</strong> ({{ $health['failed_jobs'] }} gagal)</a>
            </div>
        </div>

        <!-- Storage & Disk Monitor -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Penyimpanan &amp; Lampiran</span>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold {{ $health['storage_linked'] ? 'bg-blue-50 text-[#005B9C] border border-blue-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                    {{ $health['storage_linked'] ? 'SYMLINK AKTIF' : 'SYMLINK BELUM ADA' }}
                </span>
            </div>
            <div>
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl font-black text-[#002B49]">{{ $health['upload_size_human'] }}</p>
                    <p class="text-xs font-bold text-slate-600">{{ $health['upload_files_count'] }} berkas unggahan</p>
                </div>
                <div class="mt-3 space-y-1.5">
                    <div class="flex justify-between text-[11px] font-bold text-slate-600">
                        <span>Kapasitas Partisi Server ({{ $health['disk_used_pct'] }}%)</span>
                        <span>Sisa {{ $health['disk_free_human'] }} dari {{ $health['disk_total_human'] }}</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $health['disk_used_pct'] > 85 ? 'bg-rose-600' : 'bg-[#005B9C]' }}" style="width: {{ min(100, $health['disk_used_pct']) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Log & Runtime -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Kesehatan Runtime &amp; Log</span>
                <a href="{{ route('superadmin.logs') }}" class="text-xs font-extrabold text-[#005B9C] hover:underline">
                    Buka Log Viewer
                </a>
            </div>
            <div class="flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black {{ $health['error_log_count'] > 0 ? 'text-rose-600' : 'text-[#002B49]' }}">
                        {{ $health['error_log_count'] }} Error
                    </p>
                    <p class="text-xs font-semibold text-slate-600 mt-1">Ukuran berkas log: <strong>{{ $health['log_size_human'] }}</strong></p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-500">Laravel {{ $health['laravel_version'] }}</p>
                    <p class="text-xs font-extrabold text-slate-800 mt-0.5">PHP {{ $health['php_version'] }}</p>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-600">
                <span>Env: <strong class="uppercase">{{ $health['environment'] }}</strong></span>
                <span>APP_DEBUG: <strong class="{{ $health['debug_mode'] ? 'text-amber-700' : 'text-emerald-700' }}">{{ $health['debug_mode'] ? 'TRUE' : 'FALSE' }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Utilitas Optimasi Cache & Distribusi Akun -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kontrol Cache & System Optimizer (7 kolom) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/70 shadow-xs space-y-5">
            <div>
                <h2 class="text-lg font-extrabold text-[#002B49] tracking-tight">Utilitas Cache &amp; Optimasi Sistem</h2>
                <p class="text-xs font-semibold text-slate-500 mt-1">Jalankan pembersihan cache secara langsung setelah perubahan konfigurasi atau pembaruan kode.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <form action="{{ route('superadmin.system.optimize') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="optimize_clear">
                    <button type="submit" class="w-full p-4 rounded-2xl border border-slate-200 hover:border-[#005B9C] hover:bg-blue-50/40 text-left transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900 group-hover:text-[#005B9C]">Bersihkan Semua Cache</span>
                            <i class="fa-solid fa-broom text-[#005B9C]"></i>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 mt-1">Menjalankan <code>artisan optimize:clear</code> (config, route, view, cache).</p>
                    </button>
                </form>

                <form action="{{ route('superadmin.system.optimize') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="cache_clear">
                    <button type="submit" class="w-full p-4 rounded-2xl border border-slate-200 hover:border-[#005B9C] hover:bg-blue-50/40 text-left transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900 group-hover:text-[#005B9C]">Segarkan Cache Data</span>
                            <i class="fa-solid fa-arrows-rotate text-[#005B9C]"></i>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 mt-1">Menghapus cache query &amp; memuat ulang pengaturan sistem.</p>
                    </button>
                </form>

                <form action="{{ route('superadmin.system.optimize') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="view_clear">
                    <button type="submit" class="w-full p-4 rounded-2xl border border-slate-200 hover:border-[#005B9C] hover:bg-blue-50/40 text-left transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900 group-hover:text-[#005B9C]">Kompilasi Ulang Blade</span>
                            <i class="fa-solid fa-code text-[#005B9C]"></i>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 mt-1">Menjalankan <code>artisan view:clear</code> untuk merender ulang tampilan.</p>
                    </button>
                </form>

                <form action="{{ route('superadmin.system.optimize') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="storage_link">
                    <button type="submit" class="w-full p-4 rounded-2xl border border-slate-200 hover:border-[#005B9C] hover:bg-blue-50/40 text-left transition group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-900 group-hover:text-[#005B9C]">Verifikasi Storage Symlink</span>
                            <i class="fa-solid fa-link text-[#005B9C]"></i>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 mt-1">Memastikan folder <code>public/storage</code> terhubung ke berkas unggahan.</p>
                    </button>
                </form>
            </div>

            <div class="pt-3 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase text-slate-500 block">Cache Driver</span>
                    <strong class="font-extrabold text-slate-800">{{ $health['cache_driver'] }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase text-slate-500 block">Session Driver</span>
                    <strong class="font-extrabold text-slate-800">{{ $health['session_driver'] }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase text-slate-500 block">Queue Driver</span>
                    <strong class="font-extrabold text-slate-800">{{ $health['queue_driver'] }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase text-slate-500 block">Mail Mailer</span>
                    <strong class="font-extrabold text-slate-800">{{ $health['mail_driver'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Ringkasan Populasi Role Akun (5 kolom) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/70 shadow-xs flex flex-col justify-between space-y-5">
            <div>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-[#002B49] tracking-tight">Populasi Akun Berdasarkan Role</h2>
                    <div class="flex items-center gap-3 text-xs font-extrabold">
                        <a href="{{ route('superadmin.roles') }}" class="text-[#005B9C] hover:underline">+ Role</a>
                        <a href="{{ route('superadmin.users') }}" class="text-slate-600 hover:text-[#005B9C] hover:underline">Akun</a>
                    </div>
                </div>
                <p class="text-xs font-semibold text-slate-500 mt-1">Distribusi akun terdaftar per role sistem maupun role kustom.</p>
            </div>

            <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                @foreach($rolesSummary as $rItem)
                    <a href="{{ route('superadmin.users', ['role' => $rItem['slug']]) }}"
                       class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-blue-50/40 border border-slate-200/70 transition">
                        <div class="min-w-0 pr-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-extrabold {{ $rItem['badge_classes'] }}">{{ $rItem['name'] }}</span>
                                @if(!$rItem['is_system'])
                                    <span class="text-[10px] font-extrabold uppercase text-emerald-700">Kustom</span>
                                @endif
                            </div>
                            @if(!empty($rItem['description']))
                                <p class="text-[11px] font-medium text-slate-500 truncate mt-1">{{ $rItem['description'] }}</p>
                            @endif
                        </div>
                        <span class="text-lg font-black text-[#002B49] shrink-0">{{ $rItem['count'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-600">
                <span>Akun Dinonaktifkan:</span>
                <strong class="{{ $roleCounts['inactive'] > 0 ? 'text-rose-600' : 'text-slate-800' }}">{{ $roleCounts['inactive'] }} akun</strong>
            </div>
        </div>
    </div>

    <!-- Jejak Audit Keamanan Terbaru -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/70 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-extrabold text-[#002B49] tracking-tight">Rekam Jejak Keamanan Terbaru</h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">8 aktivitas sensitif terakhir yang tercatat secara otomatis di sistem.</p>
            </div>
            <a href="{{ route('superadmin.audit_logs') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-extrabold text-slate-800 transition self-start sm:self-auto">
                Lihat Seluruh Audit Log
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-3 pr-4">Waktu</th>
                        <th class="py-3 px-4">Pelaku</th>
                        <th class="py-3 px-4">Modul &amp; Aksi</th>
                        <th class="py-3 px-4">Rincian Aktivitas</th>
                        <th class="py-3 pl-4 text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($recentAudits as $audit)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3.5 pr-4 whitespace-nowrap font-semibold text-slate-600">
                                {{ $audit->created_at?->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900">{{ $audit->user?->name ?? 'Sistem' }}</span>
                                @if($audit->impersonator)
                                    <span class="block text-[10px] font-bold text-amber-700">via {{ $audit->impersonator->name }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 font-bold text-[11px]">{{ $audit->action }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-700 min-w-[260px]">
                                {{ $audit->description }}
                            </td>
                            <td class="py-3.5 pl-4 text-right whitespace-nowrap font-mono text-[11px] text-slate-500">
                                {{ $audit->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs font-semibold text-slate-500">
                                Belum ada catatan aktivitas audit. Aktivitas login, perubahan maintenance, dan manajemen akun akan tampil di sini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
