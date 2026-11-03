@extends('layouts.app')

@section('title', 'E-Sertifikat & Evaluasi Magang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-certificate text-blue-600"></i>
                <span>E-Sertifikat & Transkrip Nilai Magang</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Status kelulusan, evaluasi kompetensi dari pembimbing, dan sertifikat resmi magang Pelindo.</p>
        </div>

        @if($certificate && $certificate->status === 'published')
        <a href="{{ route('certificate.download', ['token' => $certificate->verification_token]) }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-2xl shadow-lg shadow-blue-500/25 transition text-xs">
            <i class="fa-solid fa-file-pdf"></i>
            <span>Unduh E-Sertifikat Resmi</span>
        </a>
        @endif
    </div>

    @if($certificate && $certificate->status === 'published')
    <!-- Published Banner Card -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-600/20 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-white">
                    <i class="fa-solid fa-circle-check text-emerald-300"></i>
                    <span>Sertifikat Resmi Terverifikasi</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight">Selamat, Program Magang Anda Telah Selesai!</h2>
                <p class="text-xs text-blue-100 max-w-xl">
                    E-Sertifikat Anda telah diterbitkan secara resmi oleh PT Pelabuhan Indonesia (Persero) dengan nomor registrasi <strong>{{ $certificate->certificate_number }}</strong>.
                </p>
            </div>

            <div class="bg-white/10 backdrop-blur border border-white/20 p-4 rounded-2xl text-center min-w-[150px]">
                <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider block">Predikat Akhir</span>
                <span class="text-2xl font-black block mt-0.5">{{ $assessment->predicate }}</span>
                <span class="text-[11px] text-blue-100 font-semibold block">Skor: {{ number_format($assessment->final_score, 1) }}/100</span>
            </div>
        </div>

        <!-- Public Verification URL Bar -->
        <div class="mt-6 pt-5 border-t border-white/15 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <span class="text-blue-100">Tautan Verifikasi Keaslian Dokumen:</span>
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <input type="text" readonly value="{{ route('certificate.verify', ['token' => $certificate->verification_token]) }}" class="bg-white/10 border border-white/20 text-white rounded-xl px-3 py-1.5 text-[11px] w-full sm:w-80 outline-none" id="verifyUrlInput">
                <button onclick="navigator.clipboard.writeText(document.getElementById('verifyUrlInput').value); alert('Tautan verifikasi disalin!');" class="bg-white text-blue-700 font-bold px-3 py-1.5 rounded-xl hover:bg-blue-50 transition text-[11px] whitespace-nowrap">
                    <i class="fa-regular fa-copy mr-1"></i> Salin
                </button>
            </div>
        </div>
    </div>
    @elseif($assessment && $assessment->status === 'submitted')
    <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6 text-amber-900 flex items-start space-x-4">
        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div>
            <h3 class="text-sm font-extrabold text-amber-900">Evaluasi Pembimbing Selesai — Menunggu Verifikasi HR</h3>
            <p class="text-xs text-amber-700 mt-1">Pembimbing telah mengisi evaluasi dan nilai akhir Anda. E-Sertifikat resmi sedang dalam proses verifikasi dan penomoran oleh tim HR Pelindo.</p>
        </div>
    </div>
    @else
    <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 text-slate-600 flex items-start space-x-4">
        <div class="w-10 h-10 rounded-2xl bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-lg shrink-0">
            <i class="fa-solid fa-spinner"></i>
        </div>
        <div>
            <h3 class="text-sm font-extrabold text-slate-800">Program Magang Masih Berjalan</h3>
            <p class="text-xs text-slate-500 mt-1">Penilaian akhir dan penerbitan e-Sertifikat akan dilakukan oleh Pembimbing Lapangan dan HR menjelang akhir periode magang Anda.</p>
        </div>
    </div>
    @endif

    <!-- Final Project Submission Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-file-arrow-up text-blue-600"></i>
                <span>Laporan Akhir Magang & Karya Final Project</span>
            </h3>
            @if($assessment?->submitted_at)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i class="fa-solid fa-check"></i> Sudah Diunggah
            </span>
            @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                <i class="fa-solid fa-clock"></i> Belum Diunggah
            </span>
            @endif
        </div>

        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <form action="{{ route('intern.certificate.final_project') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Judul Laporan Akhir / Final Project <span class="text-rose-500">*</span></label>
                <input type="text" name="final_project_title" required value="{{ old('final_project_title', $assessment?->final_project_title) }}" placeholder="Contoh: Otomasi Presensi & Sistem Manajemen Magang Pelindo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Berkas Laporan (PDF / DOCX) (Maks 10MB)</label>
                    <input type="file" name="final_project_file" accept=".pdf,.docx,.zip,.rar" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @if($assessment?->final_project_file)
                    <div class="mt-2 text-xs">
                        <a href="{{ asset($assessment->final_project_file) }}" target="_blank" class="text-blue-600 hover:underline font-bold inline-flex items-center gap-1">
                            <i class="fa-solid fa-paperclip"></i> Lihat Berkas Terunggah
                        </a>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tautan Slide Presentasi / Repository (Opsional)</label>
                    <input type="url" name="final_project_link" value="{{ old('final_project_link', $assessment?->final_project_link) }}" placeholder="https://drive.google.com/... atau GitHub" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <button type="submit" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Simpan Laporan Akhir</span>
            </button>
        </form>
    </div>

    <!-- 5 Competencies Score Breakdown (if assessed) -->
    @if($assessment)
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
            <i class="fa-solid fa-chart-simple text-blue-600"></i>
            <span>Transkrip Capaian 5 Aspek Kompetensi</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Penguasaan Teknis (30%)</span>
                <span class="text-xl font-black text-blue-600 block mt-1">{{ $assessment->technical_skill }} / 100</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kedisiplinan (20%)</span>
                <span class="text-xl font-black text-blue-600 block mt-1">{{ $assessment->discipline_score }} / 100</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Inisiatif & Solusi (15%)</span>
                <span class="text-xl font-black text-blue-600 block mt-1">{{ $assessment->initiative_score }} / 100</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kerja Sama Tim (15%)</span>
                <span class="text-xl font-black text-blue-600 block mt-1">{{ $assessment->teamwork_score }} / 100</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Sikap & Integritas (20%)</span>
                <span class="text-xl font-black text-blue-600 block mt-1">{{ $assessment->attitude_score }} / 100</span>
            </div>
            <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100">
                <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Rata-rata Terbobot</span>
                <span class="text-xl font-black text-blue-700 block mt-1">{{ number_format($assessment->final_score, 1) }} / 100</span>
            </div>
        </div>

        <!-- Feedback from supervisor -->
        @if($assessment->notes_strengths || $assessment->notes_improvements)
        <div class="pt-4 border-t border-slate-100 space-y-4">
            @if($assessment->notes_strengths)
            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5 mb-1">
                    <i class="fa-solid fa-thumbs-up"></i>
                    <span>Kelebihan & Catatan Positif Pembimbing</span>
                </span>
                <p class="text-xs text-emerald-900 leading-relaxed">{{ $assessment->notes_strengths }}</p>
            </div>
            @endif

            @if($assessment->notes_improvements)
            <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100">
                <span class="text-xs font-bold text-blue-800 flex items-center gap-1.5 mb-1">
                    <i class="fa-solid fa-lightbulb"></i>
                    <span>Saran Pengembangan Diri</span>
                </span>
                <p class="text-xs text-blue-900 leading-relaxed">{{ $assessment->notes_improvements }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif
</div>
@endsection