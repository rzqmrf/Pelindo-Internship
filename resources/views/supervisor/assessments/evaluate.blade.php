@extends('layouts.app')

@section('title', 'Evaluasi Kinerja - ' . $intern->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs font-semibold text-slate-400">
        <a href="{{ route('supervisor.assessments.index') }}" class="hover:text-blue-600 transition">Penilaian Magang</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <span class="text-slate-800">Evaluasi Peserta</span>
    </div>

    <!-- Intern Info Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-blue-400 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-blue-500/20">
                {{ substr($intern->name, 0, 2) }}
            </div>
            <div>
                <h1 class="text-lg font-black text-slate-900 leading-snug">{{ $intern->name }}</h1>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 mt-1">
                    <span>NIM/NIS: <strong>{{ $intern->internProfile?->student_id_number ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span>{{ $intern->internProfile?->institution ?? '-' }}</span>
                    <span>&bull;</span>
                    <span class="text-blue-600 font-semibold">{{ $intern->internProfile?->division?->name ?? 'Divisi' }}</span>
                </div>
            </div>
        </div>

        <!-- Live Score Badge Preview -->
        <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-2xl text-center min-w-[140px]">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Kalkulasi Skor</span>
            <span id="previewFinalScore" class="text-2xl font-black text-blue-600 block mt-0.5">
                {{ number_format($assessment?->final_score ?? 80, 1) }}
            </span>
            <span id="previewPredicate" class="text-[11px] font-bold text-slate-700 block">
                {{ $assessment?->predicate ?? 'Baik (B)' }}
            </span>
        </div>
    </div>

    <!-- Final Project Submission Review Card -->
    @if($assessment?->final_project_title)
        <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white rounded-3xl p-6 shadow-md border border-slate-800 space-y-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30">
                    <i class="fa-solid fa-file-contract"></i> Laporan Akhir & Final Project Intern
                </span>
                <span class="text-[11px] text-slate-400">
                    Diunggah: {{ $assessment->submitted_at ? $assessment->submitted_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}
                </span>
            </div>

            <h3 class="text-base font-extrabold text-white tracking-tight">{{ $assessment->final_project_title }}</h3>

            <div class="flex flex-wrap gap-3 pt-1 text-xs">
                @if($assessment->final_project_file)
                    <a href="{{ asset($assessment->final_project_file) }}" target="_blank" class="inline-flex items-center space-x-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2 rounded-xl transition">
                        <i class="fa-solid fa-download"></i>
                        <span>Unduh Berkas Laporan</span>
                    </a>
                @endif

                @if($assessment->final_project_link)
                    <a href="{{ $assessment->final_project_link }}" target="_blank" class="inline-flex items-center space-x-1.5 bg-white/10 hover:bg-white/20 text-white font-bold px-4 py-2 rounded-xl border border-white/20 transition">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Buka Slide / Repository Project</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Evaluation Form -->
    <form action="{{ route('supervisor.assessments.store', ['intern_id' => $intern->id]) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Rubric Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">
            <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                <i class="fa-solid fa-list-check text-blue-600"></i>
                <span>Rubrik Penilaian 5 Pilar Kompetensi Magang</span>
            </h2>

            <div class="space-y-5">
                <!-- 1. Technical Skill (30%) -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-xs font-bold text-slate-800 block">1. Penguasaan Tugas & Keterampilan Teknis (Bobot 30%)</label>
                            <span class="text-[11px] text-slate-500">Kemampuan memahami tugas, keahlian teknis operasional, dan hasil kerja.</span>
                        </div>
                        <span id="val_tech" class="text-sm font-black text-blue-600 min-w-[36px] text-right">{{ $assessment?->technical_skill ?? 85 }}</span>
                    </div>
                    <input type="range" name="technical_skill" id="input_tech" min="0" max="100" value="{{ $assessment?->technical_skill ?? 85 }}" class="w-full accent-blue-600 cursor-pointer" oninput="updateScores()">
                </div>

                <!-- 2. Discipline (20%) -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-xs font-bold text-slate-800 block">2. Kedisiplinan & Ketepatan Waktu (Bobot 20%)</label>
                            <span class="text-[11px] text-slate-500">Kehadiran harian, kepatuhan jam kerja, dan ketepatan pengisian logbook.</span>
                        </div>
                        <span id="val_disc" class="text-sm font-black text-blue-600 min-w-[36px] text-right">{{ $assessment?->discipline_score ?? 85 }}</span>
                    </div>
                    <input type="range" name="discipline_score" id="input_disc" min="0" max="100" value="{{ $assessment?->discipline_score ?? 85 }}" class="w-full accent-blue-600 cursor-pointer" oninput="updateScores()">
                </div>

                <!-- 3. Initiative & Problem Solving (15%) -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-xs font-bold text-slate-800 block">3. Inisiatif & Pemecahan Masalah (Bobot 15%)</label>
                            <span class="text-[11px] text-slate-500">Keaktifan bertanya, kemauan belajar hal baru, dan solusi pemecahan kendala.</span>
                        </div>
                        <span id="val_init" class="text-sm font-black text-blue-600 min-w-[36px] text-right">{{ $assessment?->initiative_score ?? 80 }}</span>
                    </div>
                    <input type="range" name="initiative_score" id="input_init" min="0" max="100" value="{{ $assessment?->initiative_score ?? 80 }}" class="w-full accent-blue-600 cursor-pointer" oninput="updateScores()">
                </div>

                <!-- 4. Teamwork & Communication (15%) -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-xs font-bold text-slate-800 block">4. Komunikasi & Kerja Sama Tim (Bobot 15%)</label>
                            <span class="text-[11px] text-slate-500">Interaksi dengan tim divisi, koordinasi kerja, dan keterbukaan menerima saran.</span>
                        </div>
                        <span id="val_team" class="text-sm font-black text-blue-600 min-w-[36px] text-right">{{ $assessment?->teamwork_score ?? 85 }}</span>
                    </div>
                    <input type="range" name="teamwork_score" id="input_team" min="0" max="100" value="{{ $assessment?->teamwork_score ?? 85 }}" class="w-full accent-blue-600 cursor-pointer" oninput="updateScores()">
                </div>

                <!-- 5. Attitude & Integrity (20%) -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-xs font-bold text-slate-800 block">5. Sikap Kerja & Integritas (Bobot 20%)</label>
                            <span class="text-[11px] text-slate-500">Etika sopan santun, kepatuhan budaya AKHLAK Pelindo, dan rasa tanggung jawab.</span>
                        </div>
                        <span id="val_atti" class="text-sm font-black text-blue-600 min-w-[36px] text-right">{{ $assessment?->attitude_score ?? 90 }}</span>
                    </div>
                    <input type="range" name="attitude_score" id="input_atti" min="0" max="100" value="{{ $assessment?->attitude_score ?? 90 }}" class="w-full accent-blue-600 cursor-pointer" oninput="updateScores()">
                </div>
            </div>
        </div>

        <!-- Qualitative Feedback Card -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-5">
            <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                <i class="fa-solid fa-comment-dots text-blue-600"></i>
                <span>Catatan Kualitatif & Rekomendasi Karir</span>
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-800 block mb-1">Kelebihan & Prestasi Utama</label>
                    <textarea name="notes_strengths" rows="3" class="w-full text-xs rounded-2xl border border-slate-200 p-3.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition" placeholder="Contoh: Peserta sangat cepat menguasai alur sistem operasional dan aktif berkoordinasi...">{{ old('notes_strengths', $assessment?->notes_strengths) }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-800 block mb-1">Saran / Area Pengembangan Diri</label>
                    <textarea name="notes_improvements" rows="3" class="w-full text-xs rounded-2xl border border-slate-200 p-3.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition" placeholder="Contoh: Tingkatkan lagi kemampuan analisis data lanjutan dan public speaking saat presentasi...">{{ old('notes_improvements', $assessment?->notes_improvements) }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-800 block mb-1">Rekomendasi Pembimbing untuk Talent Pool Pelindo</label>
                    <textarea name="supervisor_recommendation" rows="2" class="w-full text-xs rounded-2xl border border-slate-200 p-3.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition" placeholder="Contoh: Sangat direkomendasikan untuk program rekrutmen masa depan Pelindo Group...">{{ old('supervisor_recommendation', $assessment?->supervisor_recommendation) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('supervisor.assessments.index') }}" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" name="action" value="draft" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <i class="fa-regular fa-floppy-disk mr-1.5"></i> Simpan Draft
            </button>
            <button type="submit" name="action" value="submit" class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-lg shadow-blue-500/25 transition">
                <i class="fa-solid fa-paper-plane mr-1.5"></i> Kirim Nilai ke HR
            </button>
        </div>
    </form>
</div>

<script>
function updateScores() {
    const tech = parseInt(document.getElementById('input_tech').value) || 0;
    const disc = parseInt(document.getElementById('input_disc').value) || 0;
    const init = parseInt(document.getElementById('input_init').value) || 0;
    const team = parseInt(document.getElementById('input_team').value) || 0;
    const atti = parseInt(document.getElementById('input_atti').value) || 0;

    document.getElementById('val_tech').innerText = tech;
    document.getElementById('val_disc').innerText = disc;
    document.getElementById('val_init').innerText = init;
    document.getElementById('val_team').innerText = team;
    document.getElementById('val_atti').innerText = atti;

    const finalScore = (tech * 0.30) + (disc * 0.20) + (init * 0.15) + (team * 0.15) + (atti * 0.20);
    document.getElementById('previewFinalScore').innerText = finalScore.toFixed(1);

    let predicate = 'Kurang (D)';
    if (finalScore >= 85) predicate = 'Sangat Baik (A)';
    else if (finalScore >= 75) predicate = 'Baik (B)';
    else if (finalScore >= 65) predicate = 'Cukup (C)';

    document.getElementById('previewPredicate').innerText = predicate;
}
</script>
@endsection
