@extends('layouts.app')

@section('page_title', 'Survei Kepuasan Magang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-slate-900 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 space-y-2">
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-blue-200">
                <i class="fa-solid fa-star text-amber-400"></i>
                <span>Feedback & Survei Pelindo PRO</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight">Survei Kepuasan Pengalaman Magang</h1>
            <p class="text-xs text-blue-100/80 max-w-xl leading-relaxed">
                Bantu kami meningkatkan kualitas program magang PT Pelabuhan Indonesia (Persero) dengan memberikan ulasan jujur Anda.
            </p>
        </div>
    </div>

    <!-- Survey Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
        @if($existingSurvey)
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-900 text-xs font-bold">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <div>
                    <p class="font-extrabold text-sm">Anda telah mengisi survei kepuasan ini!</p>
                    <p class="text-[11px] text-emerald-700 font-medium">Terakhir diperbarui pada {{ $existingSurvey->updated_at->isoFormat('D MMMM YYYY, HH:mm') }} WIB.</p>
                </div>
            </div>
        @endif

        <form action="{{ route('intern.survey.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Program Satisfaction -->
            <div class="space-y-2">
                <label class="block text-xs font-extrabold text-slate-800">1. Tingkat Kepuasan Program Magang Secara Keseluruhan</label>
                <div class="grid grid-cols-5 gap-3">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-blue-500 cursor-pointer transition text-center group">
                            <input type="radio" name="satisfaction_rating" value="{{ $i }}" class="sr-only peer" {{ ($existingSurvey?->satisfaction_rating ?? 5) == $i ? 'checked' : '' }}>
                            <span class="text-lg mb-1 group-hover:scale-110 transition peer-checked:scale-125">
                                {{ $i === 1 ? '🙁' : ($i === 2 ? '😐' : ($i === 3 ? '🙂' : ($i === 4 ? '😊' : '🤩'))) }}
                            </span>
                            <span class="text-[10px] font-extrabold text-slate-600 peer-checked:text-blue-600">Bintang {{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <!-- 2. Supervisor Guidance -->
            <div class="space-y-2">
                <label class="block text-xs font-extrabold text-slate-800">2. Kualitas Bimbingan Pembimbing Lapangan</label>
                <div class="grid grid-cols-5 gap-3">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-blue-500 cursor-pointer transition text-center group">
                            <input type="radio" name="supervisor_rating" value="{{ $i }}" class="sr-only peer" {{ ($existingSurvey?->supervisor_rating ?? 5) == $i ? 'checked' : '' }}>
                            <span class="text-lg mb-1 group-hover:scale-110 transition peer-checked:scale-125">
                                {{ $i === 1 ? '🙁' : ($i === 2 ? '😐' : ($i === 3 ? '🙂' : ($i === 4 ? '😊' : '🤩'))) }}
                            </span>
                            <span class="text-[10px] font-extrabold text-slate-600 peer-checked:text-blue-600">Bintang {{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <!-- 3. Facility Rating -->
            <div class="space-y-2">
                <label class="block text-xs font-extrabold text-slate-800">3. Fasilitas & Dukungan Divisi Tempat Magang</label>
                <div class="grid grid-cols-5 gap-3">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="flex flex-col items-center justify-center p-3 rounded-2xl border border-slate-200 hover:border-blue-500 cursor-pointer transition text-center group">
                            <input type="radio" name="facility_rating" value="{{ $i }}" class="sr-only peer" {{ ($existingSurvey?->facility_rating ?? 5) == $i ? 'checked' : '' }}>
                            <span class="text-lg mb-1 group-hover:scale-110 transition peer-checked:scale-125">
                                {{ $i === 1 ? '🙁' : ($i === 2 ? '😐' : ($i === 3 ? '🙂' : ($i === 4 ? '😊' : '🤩'))) }}
                            </span>
                            <span class="text-[10px] font-extrabold text-slate-600 peer-checked:text-blue-600">Bintang {{ $i }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <!-- 4. Recommend Score (NPS 1-10) -->
            <div class="space-y-2">
                <label class="block text-xs font-extrabold text-slate-800">4. Seberapa Mungkin Anda Merekomendasikan Magang Pelindo ke Rekan Kampus? (Skala 1-10)</label>
                <input type="range" name="recommend_score" min="1" max="10" value="{{ $existingSurvey?->recommend_score ?? 10 }}" 
                       oninput="document.getElementById('scoreVal').innerText = this.value"
                       class="w-full accent-blue-600 cursor-pointer">
                <div class="flex items-center justify-between text-[10px] font-extrabold text-slate-400">
                    <span>1 (Sangat Tidak Mungkin)</span>
                    <span class="text-sm font-black text-blue-600" id="scoreVal">{{ $existingSurvey?->recommend_score ?? 10 }} / 10</span>
                    <span>10 (Sangat Direkomendasikan)</span>
                </div>
            </div>

            <!-- 5. Qualitative Feedback Notes -->
            <div class="space-y-2">
                <label class="block text-xs font-extrabold text-slate-800">5. Masukan / Saran Perbaikan untuk Program Magang Pelindo (Opsional)</label>
                <textarea name="feedback_notes" rows="4" 
                          placeholder="Tuliskan pengalaman positif atau kritik membangun Anda selama magang di Pelindo..."
                          class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">{{ $existingSurvey?->feedback_notes }}</textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold text-xs rounded-2xl transition shadow-lg shadow-blue-500/20 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Simpan & Kirim Ulasan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
