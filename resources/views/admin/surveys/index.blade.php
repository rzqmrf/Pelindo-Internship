@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">Rekap Survei Kepuasan Magang</h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Evaluasi umpan balik peserta terhadap program magang, kualitas bimbingan mentor, dan fasilitas kerja Pelindo.
            </p>
        </div>
        <div class="px-4 py-2.5 rounded-2xl bg-blue-50 border border-blue-200 text-xs font-extrabold text-[#005B9C] shrink-0">
            Total Responden: {{ $totalResponses }} Peserta
        </div>
    </div>

    <!-- 4 Kartu Rata-rata Nyata -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-3xl p-5 border border-slate-200/70 shadow-xs space-y-1">
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Kepuasan Program</p>
            <p class="text-3xl font-black text-[#002B49]">{{ $totalResponses > 0 ? $avgSatisfaction . ' / 5' : '-' }}</p>
            <p class="text-xs font-semibold text-slate-500">Rata-rata pengalaman umum</p>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/70 shadow-xs space-y-1">
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Kualitas Pembimbing</p>
            <p class="text-3xl font-black text-[#002B49]">{{ $totalResponses > 0 ? $avgSupervisor . ' / 5' : '-' }}</p>
            <p class="text-xs font-semibold text-slate-500">Pendampingan mentor divisi</p>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/70 shadow-xs space-y-1">
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Fasilitas &amp; Lingkungan</p>
            <p class="text-3xl font-black text-[#002B49]">{{ $totalResponses > 0 ? $avgFacility . ' / 5' : '-' }}</p>
            <p class="text-xs font-semibold text-slate-500">Sarana kerja &amp; dukungan tim</p>
        </div>

        <div class="bg-white rounded-3xl p-5 border border-slate-200/70 shadow-xs space-y-1">
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Skor Rekomendasi (NPS)</p>
            <p class="text-3xl font-black text-[#005B9C]">{{ $totalResponses > 0 ? $avgRecommend . ' / 10' : '-' }}</p>
            <p class="text-xs font-semibold text-slate-500">Kesediaan merekomendasikan</p>
        </div>
    </div>

    <!-- Filter Divisi & Kata Kunci -->
    <form action="{{ route('admin.surveys.index') }}" method="GET"
          class="bg-white rounded-3xl p-5 border border-slate-200/70 shadow-xs grid grid-cols-1 sm:grid-cols-12 gap-3">
        <div class="sm:col-span-6 relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama peserta atau isi saran/masukan..."
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C]">
        </div>
        <div class="sm:col-span-4">
            <select name="division_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 focus:outline-none focus:border-[#005B9C]">
                <option value="">Semua Divisi Penempatan</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id }}" @selected((string) request('division_id') === (string) $div->id)>{{ $div->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2 flex gap-2">
            <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'division_id']))
                <a href="{{ route('admin.surveys.index') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Tabel Hasil Survei -->
    <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-4 px-6">Peserta &amp; Divisi</th>
                        <th class="py-4 px-4 text-center">Program</th>
                        <th class="py-4 px-4 text-center">Mentor</th>
                        <th class="py-4 px-4 text-center">Fasilitas</th>
                        <th class="py-4 px-4 text-center">NPS</th>
                        <th class="py-4 px-6">Masukan &amp; Saran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($surveys as $sv)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-4 px-6 whitespace-nowrap">
                                <p class="font-extrabold text-slate-900">{{ $sv->intern?->name ?? 'Peserta' }}</p>
                                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">
                                    {{ $sv->intern?->internProfile?->division?->name ?? '-' }}
                                </p>
                            </td>
                            <td class="py-4 px-4 text-center font-extrabold text-slate-800">{{ $sv->satisfaction_rating }} / 5</td>
                            <td class="py-4 px-4 text-center font-extrabold text-slate-800">{{ $sv->supervisor_rating }} / 5</td>
                            <td class="py-4 px-4 text-center font-extrabold text-slate-800">{{ $sv->facility_rating }} / 5</td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-[#005B9C] font-extrabold">{{ $sv->recommend_score }} / 10</span>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-700 min-w-[260px] leading-relaxed">
                                {{ $sv->feedback_notes ?: 'Tidak ada catatan tambahan.' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center space-y-1.5">
                                <p class="text-sm font-extrabold text-slate-700">Belum ada data survei kepuasan yang masuk.</p>
                                <p class="text-xs font-medium text-slate-500">Hasil survei otomatis tampil setelah peserta magang mengisi menu Survei Kepuasan di akun masing-masing.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($surveys->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
