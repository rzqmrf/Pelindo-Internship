@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Header Halaman -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">Sesi Perangkat Aktif &amp; Antrean Job</h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Pantau sesi pengguna yang sedang masuk secara langsung (paksa logout perangkat) serta kelola antrean background job dan failed jobs.
            </p>
        </div>

        @if($sessions->where('is_current', false)->count() > 0)
            <form action="{{ route('superadmin.sessions.revoke_others') }}" method="POST"
                  onsubmit="return confirm('Putus paksa seluruh sesi login pengguna lain? Sesi Superadmin Anda saat ini tetap aman.')">
                @csrf
                <button type="submit" class="px-5 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs transition flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-power-off"></i>
                    <span>Putus Semua Sesi Pengguna Lain</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Ringkasan Status Sesi & Queue -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-2">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total Sesi Perangkat</span>
            <p class="text-2xl font-black text-[#002B49]">{{ $sessions->count() }} Sesi</p>
            <p class="text-xs font-medium text-slate-500">Data langsung dari tabel <code>sessions</code> database.</p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-2">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Job Menunggu Eksekusi</span>
            <p class="text-2xl font-black text-[#005B9C]">{{ $pendingJobsCount }} Job</p>
            <p class="text-xs font-medium text-slate-500">Antrean proses latar belakang pada tabel <code>jobs</code>.</p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-2">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Background Job Gagal</span>
            <p class="text-2xl font-black {{ $failedJobsCount > 0 ? 'text-rose-600' : 'text-emerald-700' }}">{{ $failedJobsCount }} Gagal</p>
            <p class="text-xs font-medium text-slate-500">Catatan kegagalan proses pada tabel <code>failed_jobs</code>.</p>
        </div>
    </div>

    <!-- Panel 1: Daftar Sesi Login Aktif -->
    <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-[#002B49]">Daftar Sesi Perangkat Aktif</h2>
                <p class="text-xs font-medium text-slate-500 mt-0.5">Gunakan tombol Putus Sesi untuk mengeluarkan akun secara paksa apabila terindikasi kendala keamanan atau pra-pemeliharaan.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Pengguna</th>
                        <th class="py-4 px-4">Role</th>
                        <th class="py-4 px-4">Perangkat &amp; IP</th>
                        <th class="py-4 px-4">Aktivitas Terakhir</th>
                        <th class="py-4 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($sessions as $sess)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-slate-900">{{ $sess['user_name'] }}</span>
                                    @if($sess['is_current'])
                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">Sesi Anda</span>
                                    @endif
                                </div>
                                <p class="text-slate-500 font-medium mt-0.5">{{ $sess['user_email'] }}</p>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-extrabold {{ $sess['role_badge'] }}">
                                    {{ $sess['role_label'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-slate-800">{{ $sess['device'] }}</p>
                                <p class="font-mono text-[11px] text-slate-500 mt-0.5">IP: {{ $sess['ip_address'] }}</p>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <p class="font-bold text-slate-800">{{ $sess['last_active'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $sess['last_active_exact'] }}</p>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                @if($sess['is_current'])
                                    <span class="text-[11px] font-bold text-slate-400">Sedang Digunakan</span>
                                @else
                                    <form action="{{ route('superadmin.sessions.revoke', $sess['id']) }}" method="POST"
                                          onsubmit="return confirm('Putus paksa sesi perangkat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-[11px] transition inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                            <span>Putus Sesi</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 px-6 text-center text-xs font-semibold text-slate-500">
                                Belum ada rekaman sesi aktif pada tabel <code>sessions</code> (atau driver sesi disetel ke non-database pada pengujian).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Panel 2: Antrean Job & Failed Jobs -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Antrean Tertunda (5 Kolom) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
            <div>
                <h2 class="text-base font-extrabold text-[#002B49]">Antrean Job Menunggu ({{ $pendingJobsCount }})</h2>
                <p class="text-xs font-medium text-slate-500 mt-0.5">Daftar tugas latar belakang yang sedang menunggu diproses worker.</p>
            </div>

            <div class="space-y-2.5">
                @forelse($pendingJobs as $pJob)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-extrabold text-slate-900 truncate">{{ $pJob['name'] }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Queue: <code>{{ $pJob['queue'] }}</code> | Percobaan: {{ $pJob['attempts'] }}</p>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500 shrink-0">{{ $pJob['available_at'] }}</span>
                    </div>
                @empty
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 text-center text-xs font-semibold text-slate-500">
                        Tidak ada antrean job yang tertunda saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Failed Jobs & Retry Controls (7 Kolom) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-200/70 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-extrabold text-[#002B49]">Daftar Failed Jobs ({{ $failedJobsCount }})</h2>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">Jalankan ulang (retry) atau bersihkan background job yang mengalami kegagalan.</p>
                </div>

                @if($failedJobsCount > 0)
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('superadmin.queues.retry_all') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-[11px] transition">
                                Retry Semua
                            </button>
                        </form>
                        <form action="{{ route('superadmin.queues.flush') }}" method="POST"
                              onsubmit="return confirm('Hapus seluruh daftar failed jobs?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-[11px] transition">
                                Bersihkan Semua
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="space-y-3">
                @forelse($failedJobs as $fJob)
                    <div x-data="{ openDetail: false }" class="p-4 rounded-2xl border border-rose-200 bg-rose-50/40 space-y-2.5 text-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <p class="font-extrabold text-slate-900">{{ $fJob['name'] }}</p>
                                <p class="text-[11px] text-slate-500">UUID: <code>{{ $fJob['uuid'] }}</code> | Gagal pada: {{ $fJob['failed_at'] }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="openDetail = !openDetail"
                                        class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold text-[11px]">
                                    <span x-text="openDetail ? 'Tutup Error' : 'Detail Error'"></span>
                                </button>
                                <form action="{{ route('superadmin.queues.retry', $fJob['uuid']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-[#005B9C] text-white font-extrabold text-[11px]">
                                        Retry
                                    </button>
                                </form>
                                <form action="{{ route('superadmin.queues.forget', $fJob['uuid']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-100 text-rose-800 font-extrabold text-[11px]" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="font-mono text-[11px] text-rose-900">{{ $fJob['summary'] }}</p>

                        <pre x-show="openDetail" x-cloak class="p-3 rounded-xl bg-slate-900 text-slate-100 text-[11px] font-mono overflow-x-auto max-h-56">{{ $fJob['exception'] }}</pre>
                    </div>
                @empty
                    <div class="p-6 rounded-2xl bg-emerald-50/50 border border-emerald-200/70 text-center text-xs font-semibold text-emerald-900">
                        Seluruh antrean background job berjalan sehat. Tidak ada failed job tercatat.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
