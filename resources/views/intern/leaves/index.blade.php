@extends('layouts.app')

@section('page_title', 'Pengajuan Izin & Sakit')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ fileName: '' }">

    <!-- Top Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengajuan Izin & Sakit</h1>
            <p class="text-xs text-slate-500 font-medium">Ajukan surat izin atau keterangan sakit resmi Anda ke pembimbing lapangan.</p>
        </div>

        <!-- Top Right Widget -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center space-x-3.5 min-w-[260px] shrink-0">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-regular fa-calendar-xmark"></i>
            </div>
            <div class="flex-1 space-y-0.5">
                <span class="font-extrabold text-slate-700 text-[11px] block">Batas Pengajuan</span>
                <span class="font-black text-rose-600 text-xs block">H-0 Sebelum Pukul 08.00 WIB</span>
                <p class="text-[9px] text-slate-400 font-semibold leading-none">Untuk pencatatan presensi hari yang sama</p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Side: Form (5 Cols) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs lg:col-span-5 space-y-5">
            <div class="border-b border-slate-100 pb-3.5">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-file-signature text-blue-600"></i>
                    <span>Formulir Pengajuan</span>
                </h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Isi seluruh informasi pengajuan secara lengkap.</p>
            </div>

            <!-- Flash messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-250/50 rounded-2xl flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-emerald-800 text-xs">Berhasil!</h4>
                        <p class="text-[11px] text-emerald-600 font-medium mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-250/50 rounded-2xl flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-rose-800 text-xs">Ada Kesalahan!</h4>
                        <ul class="list-disc list-inside text-[11px] text-rose-600 font-medium mt-0.5 leading-relaxed space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('intern.leaves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                @csrf

                <!-- Rentang Tanggal Izin -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-extrabold text-slate-800 mb-2">Tanggal Mulai *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                               class="w-full px-3.5 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition">
                    </div>
                    <div>
                        <label class="block font-extrabold text-slate-800 mb-2">Tanggal Selesai *</label>
                        <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required
                               class="w-full px-3.5 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition">
                    </div>
                </div>

                <!-- Tipe Pengajuan -->
                <div>
                    <label class="block font-extrabold text-slate-800 mb-2">Tipe Pengajuan *</label>
                    <div class="grid grid-cols-2 gap-3" x-data="{ type: '{{ old('type', 'izin') }}' }">
                        <!-- Izin -->
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition select-none"
                               :class="type === 'izin' ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="type" value="izin" x-model="type" class="text-blue-600 focus:ring-blue-600 mr-2.5">
                            <div class="leading-tight">
                                <h4 class="font-extrabold text-slate-800 text-xs">Izin Mandiri</h4>
                                <p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5">Keperluan Penting</p>
                            </div>
                        </label>
                        
                        <!-- Sakit -->
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition select-none"
                               :class="type === 'sakit' ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="type" value="sakit" x-model="type" class="text-blue-600 focus:ring-blue-600 mr-2.5">
                            <div class="leading-tight">
                                <h4 class="font-extrabold text-slate-800 text-xs">Sakit</h4>
                                <p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5">Surat Keterangan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Alasan Izin -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="font-extrabold text-slate-800">Alasan / Deskripsi Pengajuan *</label>
                        <span class="text-[10px] text-slate-400">Min. 10 karakter</span>
                    </div>
                    <textarea name="reason" rows="3" required
                              placeholder="Contoh: Mengalami demam tinggi dan flu sejak tadi malam, sehingga harus berobat ke klinik terdekat..."
                              class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl font-medium focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none leading-relaxed transition">{{ old('reason') }}</textarea>
                </div>

                <!-- Lampiran Surat Bukti -->
                <div class="space-y-1.5">
                    <label class="block font-extrabold text-slate-800">Lampiran Berkas / Surat Keterangan Dokter</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-5 text-center bg-slate-50/30 hover:bg-slate-50 transition relative">
                        <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg"
                               @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="space-y-1.5">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm mx-auto">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">Pilih Berkas Lampiran</p>
                            <p x-show="fileName" class="text-xs font-extrabold text-emerald-600 pt-0.5" x-text="'File: ' + fileName" x-cloak></p>
                            <p class="text-[9px] text-slate-400 font-semibold">Mendukung format PDF, PNG, atau JPG (Maksimal 5MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3.5 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="w-full py-3 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl transition font-black flex items-center justify-center space-x-1.5 shadow-sm text-xs">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        <span>Kirim Pengajuan Izin</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side: History List (7 Cols) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs lg:col-span-7 space-y-4">
            <div class="border-b border-slate-100 pb-3.5">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-history text-blue-600"></i>
                    <span>Riwayat Pengajuan</span>
                </h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Daftar seluruh pengajuan izin sakit yang pernah diajukan.</p>
            </div>

            <!-- List of leaves -->
            <div class="space-y-3.5 overflow-y-auto max-h-[580px] pr-1">
                @forelse($leaves as $leave)
                    <div class="p-4 bg-slate-50/50 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 hover:bg-slate-50 transition">
                        <div class="space-y-2 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded-md border
                                      {{ $leave->type === 'sakit' ? 'bg-red-50 text-red-650 border-red-100' : 'bg-blue-50 text-blue-650 border-blue-100' }}">
                                    {{ $leave->type }}
                                </span>
                                <span class="text-xs font-black text-slate-800">
                                    @if($leave->start_date->format('Y-m-d') === $leave->end_date->format('Y-m-d'))
                                        {{ $leave->start_date->isoFormat('dddd, D MMMM YYYY') }}
                                    @else
                                        {{ $leave->start_date->isoFormat('D MMMM YYYY') }} s.d. {{ $leave->end_date->isoFormat('D MMMM YYYY') }}
                                    @endif
                                </span>
                            </div>
                            
                            <p class="text-xs font-medium text-slate-650 leading-relaxed">{{ $leave->reason }}</p>

                            @if($leave->attachment_path)
                                <a href="{{ asset('storage/' . $leave->attachment_path) }}" target="_blank"
                                   class="inline-flex items-center space-x-1.5 px-2.5 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-[10px] font-bold hover:bg-slate-50 transition shadow-3xs">
                                    <i class="fa-solid fa-file-lines text-rose-500"></i>
                                    <span>Lihat Surat Lampiran</span>
                                </a>
                            @endif

                            @if($leave->supervisor_note)
                                <div class="bg-white/80 border border-slate-100 rounded-xl p-2.5 text-[11px] leading-relaxed">
                                    <span class="font-extrabold text-slate-700 block">Catatan Pembimbing:</span>
                                    <p class="text-slate-500 font-medium italic mt-0.5">"{{ $leave->supervisor_note }}"</p>
                                </div>
                            @endif
                        </div>

                        <!-- Status badge and actions -->
                        <div class="shrink-0 flex sm:flex-col items-start sm:items-end justify-between gap-2.5">
                            @php
                                $badgeStyle = match($leave->status) {
                                    'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                    default => 'bg-amber-50 text-amber-600 border-amber-100',
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-wider rounded-md border {{ $badgeStyle }}">
                                {{ $leave->status }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">
                                Diajukan {{ $leave->created_at ? $leave->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <div class="w-12 h-12 bg-slate-100 text-slate-450 rounded-full flex items-center justify-center text-lg mx-auto">
                            <i class="fa-regular fa-folder-open"></i>
                        </div>
                        <p class="text-xs font-bold">Belum ada riwayat pengajuan izin</p>
                        <p class="text-[10px] text-slate-400">Silakan gunakan form di sebelah kiri untuk mengajukan izin.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
