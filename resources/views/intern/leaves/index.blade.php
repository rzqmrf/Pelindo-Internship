@extends('layouts.app')

@section('page_title', 'Pengajuan Izin & Sakit')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ fileName: '' }">

    <!-- Centralized Header Section -->
    <x-page-header
        title="Pengajuan Izin & Sakit"
        subtitle="Ajukan surat izin atau keterangan sakit resmi Anda ke pembimbing lapangan."
        icon="fa-regular fa-calendar-xmark" />

    <!-- Top Right Widget Notice -->
    <div class="bg-amber-50/80 border border-amber-200/80 rounded-2xl p-5 flex items-center space-x-4 shadow-xs">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold shrink-0">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="flex-1 space-y-0.5">
            <span class="font-extrabold text-amber-900 text-sm block">Batas Pengajuan Presensi Harian</span>
            <p class="text-xs text-amber-700 font-medium">Pengajuan izin/sakit untuk hari yang sama hendaknya diajukan sebelum pukul 08.00 WIB untuk validasi otomatis presensi.</p>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Side: Form (5 Cols) -->
        <div class="lg:col-span-5">
            <x-card>
                <x-slot name="title">Formulir Pengajuan</x-slot>
                <x-slot name="subtitle">Isi seluruh informasi pengajuan secara lengkap.</x-slot>
                <x-slot name="icon">fa-solid fa-file-signature text-[#005B9C]</x-slot>

                <!-- Flash messages -->
                @if(session('success'))
                <x-alert type="success" title="Berhasil!">
                    {{ session('success') }}
                </x-alert>
                @endif

                @if($errors->any())
                <x-alert type="error" title="Ada Kesalahan!">
                    <ul class="list-disc list-inside text-xs space-y-1 mt-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
                @endif

                <form action="{{ route('intern.leaves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-sm font-semibold">
                    @csrf

                    <!-- Rentang Tanggal Izin -->
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input
                            type="date"
                            name="start_date"
                            label="Tanggal Mulai *"
                            value="{{ old('start_date', date('Y-m-d')) }}"
                            required />
                        <x-form-input
                            type="date"
                            name="end_date"
                            label="Tanggal Selesai *"
                            value="{{ old('end_date', date('Y-m-d')) }}"
                            required />
                    </div>

                    <!-- Tipe Pengajuan -->
                    <div class="space-y-2">
                        <label class="block font-bold text-slate-800 text-sm">Tipe Pengajuan *</label>
                        <div class="grid grid-cols-2 gap-3" x-data="{ type: '{{ old('type', 'izin') }}' }">
                            <!-- Izin -->
                            <label class="flex items-center p-3.5 rounded-2xl border cursor-pointer transition select-none"
                                :class="type === 'izin' ? 'bg-blue-50/60 border-[#005B9C] ring-2 ring-[#005B9C]/20' : 'bg-white border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="type" value="izin" x-model="type" class="text-[#005B9C] focus:ring-[#005B9C] mr-3">
                                <div class="leading-snug">
                                    <h4 class="font-extrabold text-slate-900 text-xs">Izin Mandiri</h4>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase mt-0.5">Keperluan Penting</p>
                                </div>
                            </label>

                            <!-- Sakit -->
                            <label class="flex items-center p-3.5 rounded-2xl border cursor-pointer transition select-none"
                                :class="type === 'sakit' ? 'bg-blue-50/60 border-[#005B9C] ring-2 ring-[#005B9C]/20' : 'bg-white border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="type" value="sakit" x-model="type" class="text-[#005B9C] focus:ring-[#005B9C] mr-3">
                                <div class="leading-snug">
                                    <h4 class="font-extrabold text-slate-900 text-xs">Sakit</h4>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase mt-0.5">Surat Dokter</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Alasan Izin -->
                    <x-form-textarea
                        name="reason"
                        label="Alasan / Deskripsi Pengajuan *"
                        placeholder="Contoh: Mengalami demam tinggi dan flu, berobat ke klinik..."
                        rows="3"
                        required>
                        {{ old('reason') }}
                    </x-form-textarea>

                    <!-- Lampiran Surat Bukti -->
                    <div class="space-y-2">
                        <label class="block font-bold text-slate-800 text-sm">Lampiran Berkas / Surat Dokter</label>
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-5 text-center bg-slate-50/50 hover:bg-slate-50 transition relative">
                            <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg"
                                @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="space-y-2">
                                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#005B9C] flex items-center justify-center text-base mx-auto">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Pilih Berkas Lampiran</p>
                                <p x-show="fileName" class="text-xs font-extrabold text-emerald-600 pt-0.5" x-text="'File: ' + fileName" x-cloak></p>
                                <p class="text-[11px] text-slate-500 font-medium">Format: PDF, PNG, atau JPG (Max. 5MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <x-button type="submit" variant="primary" class="w-full justify-center" icon="fa-solid fa-paper-plane">
                            Kirim Pengajuan Izin
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Right Side: History List (7 Cols) -->
        <div class="lg:col-span-7">
            <x-card>
                <x-slot name="title">Riwayat Pengajuan</x-slot>
                <x-slot name="subtitle">Daftar seluruh pengajuan izin sakit yang pernah diajukan.</x-slot>
                <x-slot name="icon">fa-solid fa-history text-[#005B9C]</x-slot>

                <!-- List of leaves -->
                <div class="space-y-4 overflow-y-auto max-h-[620px] pr-1">
                    @forelse($leaves as $leave)
                    <div class="p-5 bg-slate-50/70 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 transition">
                        <div class="space-y-2.5 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <x-badge :status="$leave->type" />
                                <span class="text-sm font-bold text-slate-900">
                                    @if($leave->start_date->format('Y-m-d') === $leave->end_date->format('Y-m-d'))
                                    {{ $leave->start_date->isoFormat('dddd, D MMMM YYYY') }}
                                    @else
                                    {{ $leave->start_date->isoFormat('D MMMM YYYY') }} s.d. {{ $leave->end_date->isoFormat('D MMMM YYYY') }}
                                    @endif
                                </span>
                            </div>

                            <p class="text-xs font-medium text-slate-700 leading-relaxed bg-white p-3 rounded-xl border border-slate-100">"{{ $leave->reason }}"</p>

                            @if($leave->attachment_path)
                            <a href="{{ asset('storage/' . $leave->attachment_path) }}" target="_blank"
                                class="inline-flex items-center space-x-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition shadow-2xs">
                                <i class="fa-solid fa-file-lines text-rose-500"></i>
                                <span>Lihat Surat Lampiran</span>
                            </a>
                            @endif

                            @if($leave->supervisor_note)
                            <div class="bg-amber-50/60 border border-amber-200/60 rounded-xl p-3 text-xs leading-relaxed">
                                <span class="font-bold text-amber-900 block mb-0.5">Catatan Pembimbing:</span>
                                <p class="text-amber-800 font-medium italic">"{{ $leave->supervisor_note }}"</p>
                            </div>
                            @endif
                        </div>

                        <!-- Status badge and actions -->
                        <div class="shrink-0 flex sm:flex-col items-start sm:items-end justify-between gap-2">
                            <x-badge :status="$leave->status" />
                            <span class="text-xs text-slate-400 font-medium">
                                {{ $leave->created_at ? $leave->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <x-empty-state
                        title="Belum ada riwayat pengajuan izin"
                        subtitle="Silakan gunakan formulir di sebelah kiri untuk mengajukan izin."
                        icon="fa-regular fa-folder-open" />
                    @endforelse
                </div>
            </x-card>
        </div>

    </div>

</div>
@endsection