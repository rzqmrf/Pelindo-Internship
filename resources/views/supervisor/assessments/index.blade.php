@extends('layouts.app')

@section('title', 'Penilaian & Evaluasi Peserta Magang')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-graduation-cap text-blue-600"></i>
                <span>Evaluasi & Nilai Akhir Magang</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Berikan evaluasi kompetensi dan penilaian akhir peserta bimbingan untuk penerbitan sertifikat resmi.</p>
        </div>
    </div>

    <!-- Interns Evaluation List -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Daftar Peserta Bimbingan ({{ $interns->count() }})</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Peserta Magang</th>
                        <th class="py-3.5 px-5">Instansi / Divisi</th>
                        <th class="py-3.5 px-5 text-center">Status Evaluasi</th>
                        <th class="py-3.5 px-5 text-center">Nilai Akhir</th>
                        <th class="py-3.5 px-5 text-center">E-Sertifikat</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($interns as $intern)
                    @php
                    $assessment = $intern->assessments->first();
                    $certificate = $intern->certificates->first();
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-5">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($intern->name, 0, 2) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $intern->name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $intern->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="font-semibold text-slate-800 block">{{ $intern->internProfile?->institution ?? '-' }}</span>
                            <span class="text-[11px] text-slate-500">{{ $intern->internProfile?->division?->name ?? 'Divisi Umum' }}</span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if(!$assessment)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                Belum Dinilai
                            </span>
                            @elseif($assessment->status === 'draft')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                Draft Evaluasi
                            </span>
                            @elseif($assessment->status === 'submitted')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-200">
                                Menunggu HR
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                Selesai Disetujui
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($assessment)
                            <span class="font-extrabold text-slate-900">{{ number_format($assessment->final_score, 1) }}</span>
                            <span class="text-[10px] font-semibold text-slate-500 block">({{ $assessment->predicate }})</span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($certificate)
                            <span class="inline-flex items-center space-x-1 text-emerald-600 font-bold text-[11px]">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Terbit</span>
                            </span>
                            @else
                            <span class="text-slate-400 text-[11px]">Belum terbit</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('supervisor.assessments.evaluate', ['intern_id' => $intern->id]) }}" class="inline-flex items-center space-x-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-xl font-bold text-xs transition">
                                <i class="fa-solid {{ $assessment ? 'fa-pen-to-square' : 'fa-star' }}"></i>
                                <span>{{ $assessment ? 'Edit Nilai' : 'Beri Nilai' }}</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">
                            Belum ada peserta magang yang dibimbing.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection