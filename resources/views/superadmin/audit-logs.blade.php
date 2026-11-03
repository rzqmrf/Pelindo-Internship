@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Audit Log -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs space-y-1.5">
        <h1 class="text-2xl font-black text-[#002B49] tracking-tight">Audit Keamanan &amp; Riwayat Aktivitas</h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600">
            Rekam jejak otomatis atas aktivitas autentikasi, sesi impersonasi, perubahan pemeliharaan, manajemen akun, pencairan uang saku, dan penerbitan sertifikat.
        </p>
    </div>

    <!-- Filter Pencarian Audit -->
    <form action="{{ route('superadmin.audit_logs') }}" method="GET"
          class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/70 shadow-xs grid grid-cols-1 sm:grid-cols-12 gap-3">
        <div class="sm:col-span-5 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari pelaku, deskripsi aksi, kode aksi, atau alamat IP..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C]">
        </div>

        <div class="sm:col-span-3">
            <select name="module" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 focus:outline-none focus:border-[#005B9C]">
                <option value="">Semua Modul</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}" @selected(request('module') === $mod)>Modul: {{ strtoupper($mod) }}</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <input type="date" name="date" value="{{ request('date') }}"
                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#005B9C]">
        </div>

        <div class="sm:col-span-2 flex gap-2">
            <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'module', 'date']))
                <a href="{{ route('superadmin.audit_logs') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center" title="Reset Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Tabel Audit Trail -->
    <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Waktu Kejadian</th>
                        <th class="py-4 px-4">Pelaku Eksekusi</th>
                        <th class="py-4 px-4">Modul &amp; Kode Aksi</th>
                        <th class="py-4 px-4">Deskripsi Aktivitas</th>
                        <th class="py-4 px-6 text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-4 px-6 whitespace-nowrap">
                                <p class="font-bold text-slate-800">{{ $log->created_at?->format('d/m/Y H:i:s') }}</p>
                                <p class="text-[11px] text-slate-500 font-medium">{{ $log->created_at?->diffForHumans() }}</p>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($log->user)
                                    <p class="font-extrabold text-slate-900">{{ $log->user->name }}</p>
                                    <p class="text-[11px] text-slate-500 font-semibold">{{ $log->user->roleLabel() }}</p>
                                @else
                                    <span class="font-bold text-slate-600">Sistem Otomatis</span>
                                @endif
                                @if($log->impersonator)
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded bg-amber-100 text-amber-900 text-[10px] font-extrabold">
                                        Impersonasi oleh {{ $log->impersonator->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-[#005B9C] font-extrabold text-[10px] uppercase">{{ $log->module }}</span>
                                <p class="font-mono text-[11px] font-bold text-slate-700 mt-1">{{ $log->action }}</p>
                            </td>
                            <td class="py-4 px-4 font-medium text-slate-700 min-w-[280px] leading-relaxed">
                                {{ $log->description }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <span class="font-mono text-[11px] font-semibold text-slate-600">{{ $log->ip_address ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center space-y-1.5">
                                <p class="text-sm font-extrabold text-slate-700">Belum ditemukan catatan audit yang sesuai.</p>
                                <p class="text-xs font-medium text-slate-500">Ubah parameter pencarian atau tanggal untuk melihat aktivitas pada periode lain.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
