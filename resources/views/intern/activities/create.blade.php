@extends('layouts.app')

@section('page_title', 'Form Logbook Aktivitas Harian')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Header Section matching new mockup -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Form Logbook Aktivitas Harian</h1>
            <p class="text-xs text-slate-500 font-medium">Catat pekerjaan, pembelajaran, dan bukti kegiatan yang kamu lakukan hari ini.</p>
        </div>

        <!-- Top Right Progress Magang Widget Card -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center space-x-3.5 min-w-[260px] shrink-0">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-regular fa-clipboard"></i>
            </div>
            <div class="flex-1 space-y-1">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-extrabold text-slate-700 text-[11px]">Progress Magang</span>
                    <span class="font-black text-blue-600 text-xs">30%</span>
                </div>
                <p class="text-[10px] text-slate-500 font-bold">Hari ke-6 <span class="font-normal text-slate-400">dari 20 hari</span></p>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: 30%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Info Status Cards Row matching new mockup -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- Card 1: Tanggal Hari Ini -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Tanggal Hari Ini</p>
                <h3 class="text-sm font-black text-slate-800">{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold">{{ \Carbon\Carbon::now()->isoFormat('dddd') }}</p>
            </div>
        </div>

        <!-- Card 2: Batas Pengisian -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Batas Pengisian</p>
                <h3 class="text-sm font-black text-slate-800">23.59 WIB</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Hari ini</p>
            </div>
        </div>

        <!-- Card 3: Status Hari Ini -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-xs flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-regular fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Status Hari Ini</p>
                <h3 class="text-sm font-black text-amber-500">Belum diisi</h3>
                <p class="text-[10px] text-slate-400 font-semibold">Segera lengkapi logbook Anda</p>
            </div>
        </div>

    </div>

    <!-- Main Activity Form Card matching new mockup -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-xs">
        <form action="{{ route('intern.activities.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs" x-data="{ statusKegiatan: 'WFO', fileName: '' }">
            @csrf

            <!-- Row 1: Date & Status Kegiatan Radio Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Field 1: Tanggal Kegiatan -->
                <div>
                    <label class="block font-extrabold text-slate-800 mb-2 text-xs">Tanggal Kegiatan *</label>
                    <div class="relative">
                        <input type="date" name="date" value="{{ old('date', $today) }}" required
                            class="w-full px-4 py-3 bg-slate-50/70 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition">
                    </div>
                </div>

                <!-- Field 2: Status Kegiatan 3 Cards -->
                <div>
                    <label class="block font-extrabold text-slate-800 mb-2 text-xs">Status Kegiatan *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                        <!-- Card WFO -->
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition select-none"
                            :class="statusKegiatan === 'WFO' ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="work_location" value="WFO" checked x-model="statusKegiatan" class="text-blue-600 focus:ring-blue-600 mr-2.5">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div class="leading-tight">
                                    <h4 class="font-extrabold text-slate-800 text-xs">WFO</h4>
                                    <p class="text-[8px] text-slate-400 font-semibold">Work From Office</p>
                                </div>
                            </div>
                        </label>

                        <!-- Card WFH -->
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition select-none"
                            :class="statusKegiatan === 'WFH' ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="work_location" value="WFH" x-model="statusKegiatan" class="text-blue-600 focus:ring-blue-600 mr-2.5">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-solid fa-house-laptop"></i>
                                </div>
                                <div class="leading-tight">
                                    <h4 class="font-extrabold text-slate-800 text-xs">WFH</h4>
                                    <p class="text-[8px] text-slate-400 font-semibold">Work From Home</p>
                                </div>
                            </div>
                        </label>

                        <!-- Card Izin -->
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition select-none"
                            :class="statusKegiatan === 'Leave' ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <input type="radio" name="work_location" value="Leave" x-model="statusKegiatan" class="text-blue-600 focus:ring-blue-600 mr-2.5">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                </div>
                                <div class="leading-tight">
                                    <h4 class="font-extrabold text-slate-800 text-xs">Izin</h4>
                                    <p class="text-[8px] text-slate-400 font-semibold">Tidak dapat bekerja</p>
                                </div>
                            </div>
                        </label>

                    </div>
                </div>
            </div>

            <!-- Row 2: Deskripsi Rincian Kegiatan -->
            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label class="font-extrabold text-slate-800 text-xs">Deskripsi Rincian Kegiatan *</label>
                    <span class="text-[10px] text-slate-400 font-semibold">Minimal 10 karakter</span>
                </div>
                <p class="text-[11px] text-slate-400 font-medium">Jelaskan secara rinci tugas yang dikerjakan, rapat yang diikuti, kendala, atau pencapaian Anda hari ini.</p>
                <textarea name="activity_description" rows="4" required
                    placeholder="Contoh: Melakukan integrasi API webhook untuk tracking kontainer kapal menggunakan Laravel 12 dan Redis queue..."
                    class="w-full p-4 bg-slate-50/50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none leading-relaxed transition">{{ old('activity_description') }}</textarea>
            </div>

            <!-- Row 3: Catatan / Pembelajaran Tambahan -->
            <div class="space-y-1.5">
                <label class="block font-extrabold text-slate-800 text-xs">Catatan / Pembelajaran Tambahan (Opsional)</label>
                <p class="text-[11px] text-slate-400 font-medium">Tuliskan hal baru yang dipelajari, insight, atau rencana untuk besok.</p>
                <textarea name="notes" rows="3"
                    placeholder="Contoh: Mempelajari cara menggunakan Redis queue untuk meningkatkan performa aplikasi..."
                    class="w-full p-4 bg-slate-50/50 border border-slate-200 rounded-2xl text-xs font-medium focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none leading-relaxed transition">{{ old('notes') }}</textarea>
            </div>

            <!-- Row 4: Lampiran Berkas Drag & Drop Box -->
            <div class="space-y-1.5">
                <label class="block font-extrabold text-slate-800 text-xs">Lampiran Berkas / Bukti Kegiatan (Opsional)</label>
                <p class="text-[11px] text-slate-400 font-medium">Upload dokumen, screenshot, atau file pendukung lainnya.</p>

                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center bg-slate-50/30 hover:bg-slate-50 transition relative">
                    <input type="file" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div class="space-y-2">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg mx-auto">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Drag & drop file di sini</p>
                        <p class="text-[10px] text-slate-400 font-semibold">atau</p>
                        <div>
                            <span class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-extrabold inline-block shadow-2xs">
                                Pilih File
                            </span>
                        </div>
                        <p x-show="fileName" class="text-xs font-bold text-emerald-600 pt-1" x-text="'File terpilih: ' + fileName"></p>
                        <p class="text-[10px] text-slate-400 pt-1">Format yang didukung: PDF, DOCX, PNG, JPG (Maksimal 5MB).</p>
                    </div>
                </div>
            </div>

            <!-- Row 5: Action Buttons matching screenshot -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <button type="submit" name="action" value="draft"
                    class="px-5 py-2.5 bg-white hover:bg-slate-100 text-slate-700 font-extrabold rounded-xl text-xs transition border border-slate-200 shadow-2xs flex items-center space-x-1.5">
                    <i class="fa-regular fa-bookmark"></i>
                    <span>Simpan Draft</span>
                </button>

                <button type="submit" name="action" value="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-xl text-xs transition shadow-sm flex items-center space-x-2 active:scale-98">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Logbook</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection