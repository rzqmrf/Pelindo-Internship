@extends('layouts.app')

@section('title', 'Kelola Evaluasi & E-Sertifikat Magang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-award text-blue-600"></i>
                <span>Kelola Evaluasi & E-Sertifikat Magang</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Verifikasi penilaian kinerja dari pembimbing dan terbitkan e-Sertifikat resmi Pelindo ber-QR Code.</p>
        </div>
    </div>

    <!-- Assessments Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-800">Daftar Penilaian Magang</h2>

            <!-- Filter -->
            <form method="GET" action="{{ route('admin.assessments.index') }}" class="flex items-center space-x-2">
                <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border border-slate-200 py-2 px-3 focus:border-blue-500 outline-none">
                    <option value="">Semua Status</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Menunggu Persetujuan HR</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Sertifikat Terbit</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft Pembimbing</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Peserta Magang</th>
                        <th class="py-3.5 px-5">Pembimbing</th>
                        <th class="py-3.5 px-5 text-center">Skor & Predikat</th>
                        <th class="py-3.5 px-5 text-center">Status</th>
                        <th class="py-3.5 px-5 text-center">Nomor Sertifikat</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($assessments as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($item->intern?->name ?? 'NA', 0, 2) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $item->intern?->name }}</span>
                                        <span class="text-[11px] text-slate-400">
                                            {{ $item->intern?->internProfile?->institution ?? '-' }} &bull; {{ $item->intern?->internProfile?->division?->code ?? 'PLND' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-800 block">{{ $item->supervisor?->name ?? '-' }}</span>
                                <span class="text-[11px] text-slate-500">NIP: {{ $item->supervisor?->supervisorProfile?->employee_id_number ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="font-black text-blue-600 text-sm">{{ number_format($item->final_score, 1) }}</span>
                                <span class="text-[10px] font-bold text-slate-600 block">{{ $item->predicate }}</span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($item->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        <i class="fa-solid fa-check mr-1"></i> Terbit
                                    </span>
                                @elseif($item->status === 'submitted')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 animate-pulse">
                                        Siap Terbit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($item->certificate)
                                    <span class="font-mono text-[11px] font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded-lg">
                                        {{ $item->certificate->certificate_number }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    @if($item->status === 'submitted')
                                        <form action="{{ route('admin.assessments.publish', ['id' => $item->id]) }}" method="POST" onsubmit="return confirm('Terbitkan e-Sertifikat resmi untuk peserta ini?')">
                                            @csrf
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-xl text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                                <i class="fa-solid fa-stamp"></i>
                                                <span>Terbitkan Sertifikat</span>
                                            </button>
                                        </form>
                                    @elseif($item->certificate)
                                        <a href="{{ route('certificate.download', ['token' => $item->certificate->verification_token]) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold px-3 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            <span>Lihat PDF</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                Belum ada penilaian magang yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assessments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
