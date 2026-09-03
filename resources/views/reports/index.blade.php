@extends('layouts.app')

@section('page_title', 'Cetak & Export Laporan Magang')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    step: 1,
    format: 'pdf',
    intern_id: '',
    division_id: '',
    start_date: '',
    end_date: '',
    status: 'approved',
    columns: ['nim', 'name', 'date', 'location', 'description', 'notes', 'status', 'supervisor'],
    isGenerating: false,
    generationProgress: 0,
    generationStepText: '',
    triggerDownload() {
        this.isGenerating = true;
        this.generationProgress = 0;
        
        let steps = [
            { p: 15, t: 'Menghubungkan ke server database...' },
            { p: 40, t: 'Menyaring logbook harian berdasarkan parameter...' },
            { p: 70, t: 'Mengompilasi lembar verifikasi stempel resmi Pelindo...' },
            { p: 90, t: 'Membuat berkas unduhan...' },
            { p: 100, t: 'Siap mengunduh berkas!' }
        ];

        let runStep = (index) => {
            if (index < steps.length) {
                this.generationStepText = steps[index].t;
                let duration = index === 2 ? 800 : 450; // give compile step more time
                setTimeout(() => {
                    this.generationProgress = steps[index].p;
                    runStep(index + 1);
                }, duration);
            } else {
                setTimeout(() => {
                    this.isGenerating = false;
                    document.getElementById('realExportForm').submit();
                }, 300);
            }
        };

        runStep(0);
    }
}">

    <!-- Centralized Page Header -->
    <x-page-header 
        title="Cetak & Export Laporan Magang PDF / Excel" 
        subtitle="Ekspor berkas laporan harian kegiatan magang berformat standar resmi PT Pelabuhan Indonesia (Persero) lengkap dengan tanda tangan verifikasi." 
        icon="fa-solid fa-file-export"
        badge="Official Report Center"
    />

    <!-- Step Progress Indicator -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between">
        <div class="flex items-center justify-between w-full max-w-2xl mx-auto text-xs font-black select-none">
            <!-- Step 1 Info -->
            <div class="flex items-center space-x-2 cursor-pointer" @click="step = 1">
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition"
                      :class="step === 1 ? 'bg-[#0B63E5] text-white' : (step > 1 ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-450')">
                    <template x-if="step > 1">
                        <i class="fa-solid fa-check text-xs"></i>
                    </template>
                    <template x-if="step === 1">
                        <span>1</span>
                    </template>
                </span>
                <span :class="step === 1 ? 'text-slate-800' : 'text-slate-400'">Pilih Format</span>
            </div>

            <!-- Line -->
            <div class="flex-1 h-0.5 bg-slate-100 mx-4" :class="step > 1 ? 'bg-emerald-100' : 'bg-slate-100'"></div>

            <!-- Step 2 Info -->
            <div class="flex items-center space-x-2 cursor-pointer" @click="step = Math.max(step, 2)">
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition"
                      :class="step === 2 ? 'bg-[#0B63E5] text-white' : (step > 2 ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-450')">
                    <template x-if="step > 2">
                        <i class="fa-solid fa-check text-xs"></i>
                    </template>
                    <template x-if="step <= 2">
                        <span>2</span>
                    </template>
                </span>
                <span :class="step === 2 ? 'text-slate-800' : 'text-slate-400'">Filter Parameter</span>
            </div>

            <!-- Line -->
            <div class="flex-1 h-0.5 bg-slate-100 mx-4" :class="step > 2 ? 'bg-emerald-100' : 'bg-slate-100'"></div>

            <!-- Step 3 Info -->
            <div class="flex items-center space-x-2 cursor-pointer" @click="step = Math.max(step, 3)">
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition"
                      :class="step === 3 ? 'bg-[#0B63E5] text-white' : 'bg-slate-100 text-slate-450'">
                    <span>3</span>
                </span>
                <span :class="step === 3 ? 'text-slate-800' : 'text-slate-400'">Kustomisasi & Unduh</span>
            </div>
        </div>
    </div>

    <!-- Main Wizard Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm relative min-h-[300px] overflow-hidden">
        
        <!-- Loading generation simulator overlay -->
        <div x-show="isGenerating" x-transition.opacity class="absolute inset-0 bg-white/95 z-30 flex flex-col items-center justify-center p-6 space-y-4" x-cloak>
            <div class="w-16 h-16 relative flex items-center justify-center">
                <!-- Outer pulsing glow circle -->
                <div class="absolute inset-0 rounded-full border-4 border-blue-100/50 animate-ping"></div>
                <!-- Middle spinning circle -->
                <div class="absolute inset-0 rounded-full border-4 border-t-[#0B63E5] border-r-transparent border-b-transparent border-l-transparent animate-spin"></div>
                <!-- Inner logo circle -->
                <div class="w-10 h-10 rounded-full bg-blue-50 text-[#0B63E5] flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-file-export animate-bounce"></i>
                </div>
            </div>
            <div class="space-y-1.5 text-center max-w-sm">
                <h4 class="font-extrabold text-slate-800 text-sm">Menyiapkan Laporan Anda...</h4>
                <div class="w-48 bg-slate-100 h-1.5 rounded-full mx-auto overflow-hidden">
                    <div class="bg-[#0B63E5] h-1.5 rounded-full transition-all duration-300" :style="'width: ' + generationProgress + '%'"></div>
                </div>
                <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider" x-text="generationStepText"></p>
            </div>
        </div>

        <!-- Hidden standard HTML form to submit to real controllers -->
        <form id="realExportForm" :action="format === 'pdf' ? '{{ route('reports.pdf') }}' : '{{ route('reports.excel') }}'" method="GET" :target="format === 'pdf' ? '_blank' : ''" class="hidden">
            <input type="hidden" name="intern_id" :value="intern_id">
            <input type="hidden" name="division_id" :value="division_id">
            <input type="hidden" name="start_date" :value="start_date">
            <input type="hidden" name="end_date" :value="end_date">
            <input type="hidden" name="status" :value="status">
            <template x-for="col in columns">
                <input type="hidden" name="columns[]" :value="col">
            </template>
        </form>

        <div class="space-y-6 text-xs font-bold text-slate-700">
            <!-- Step 1 Panel: Format Selection -->
            <div x-show="step === 1" class="space-y-5" x-transition>
                <div class="border-b border-slate-50 pb-3">
                    <h3 class="font-black text-slate-850 text-sm">Pilih Format Laporan</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Tentukan jenis dokumen laporan magang yang ingin diekspor.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- PDF Option -->
                    <div @click="format = 'pdf'" 
                         class="p-5 border rounded-3xl cursor-pointer transition flex items-center space-x-4 select-none"
                         :class="format === 'pdf' ? 'border-[#0B63E5] bg-blue-50/10' : 'border-slate-200 hover:bg-slate-50/50'">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-sm">Laporan PDF Resmi</h4>
                            <p class="text-[11px] text-slate-450 font-semibold mt-0.5">Format dengan kop surat resmi logo, header, & tanda tangan Pelindo.</p>
                        </div>
                    </div>

                    <!-- Excel Option -->
                    <div @click="format = 'excel'" 
                         class="p-5 border rounded-3xl cursor-pointer transition flex items-center space-x-4 select-none"
                         :class="format === 'excel' ? 'border-[#0B63E5] bg-blue-50/10' : 'border-slate-200 hover:bg-slate-50/50'">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-file-excel"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-sm">Spreadsheet Excel (.xlsx)</h4>
                            <p class="text-[11px] text-slate-450 font-semibold mt-0.5">Rekapitulasi lengkap seluruh log kegiatan untuk pengolahan spreadsheet.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex justify-end">
                    <button @click="step = 2" class="px-6 py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-xl transition font-extrabold flex items-center space-x-1.5 shadow-2xs">
                        <span>Lanjut Langkah 2</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2 Panel: Filter Parameter -->
            <div x-show="step === 2" class="space-y-5" x-transition x-cloak>
                <div class="border-b border-slate-50 pb-3">
                    <h3 class="font-black text-slate-850 text-sm">Saring Data Laporan</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Saring data logbook magang berdasarkan parameter di bawah ini.</p>
                </div>

                <div class="space-y-4">
                    <!-- Filters Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(!Auth::user()->isIntern())
                            <div>
                                <label class="block font-black text-slate-700 mb-1.5">Peserta Magang *</label>
                                <div class="relative">
                                    <select x-model="intern_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                                        <option value="">Semua Peserta</option>
                                        @foreach($interns as $intern)
                                            <option value="{{ $intern->id }}">{{ $intern->name }} ({{ $intern->internProfile?->institution ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <div>
                                <label class="block font-black text-slate-700 mb-1.5">Divisi Pelindo</label>
                                <div class="relative">
                                    <select x-model="division_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                                        <option value="">Semua Divisi</option>
                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block font-black text-slate-700 mb-1.5">Status Approval Laporan</label>
                            <div class="relative">
                                <select x-model="status" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                                    <option value="">Semua Status</option>
                                    <option value="approved">Disetujui (Approved) Saja</option>
                                    <option value="pending">Pending Saja</option>
                                    <option value="revision">Revisi Saja</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Date range -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-black text-slate-700 mb-1.5">Tanggal Mulai</label>
                                <input type="date" x-model="start_date" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                            </div>
                            <div>
                                <label class="block font-black text-slate-700 mb-1.5">Tanggal Sampai</label>
                                <input type="date" x-model="end_date" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex justify-between">
                    <button @click="step = 1" type="button" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold flex items-center space-x-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Kembali</span>
                    </button>
                    <button @click="step = 3" type="button" class="px-6 py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-xl transition font-extrabold flex items-center space-x-1.5 shadow-2xs">
                        <span>Lanjut Langkah 3</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3 Panel: Column Customization & Unduh -->
            <div x-show="step === 3" class="space-y-5" x-transition x-cloak>
                <div class="border-b border-slate-50 pb-3">
                    <h3 class="font-black text-slate-850 text-sm">Kustomisasi Kolom & Unduh Laporan</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Pilih kolom data yang ingin disertakan dalam berkas ekspor laporan Anda.</p>
                </div>

                <!-- Customization checkboxes -->
                <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-3xl space-y-3">
                    <p class="font-black text-slate-800 text-[11px] uppercase tracking-wider text-[#0B63E5]"><i class="fa-solid fa-table-columns mr-1.5"></i> Kolom yang disertakan:</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 text-xs text-slate-700 font-extrabold">
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="nim" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>NIM / NIS</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="name" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>Nama Peserta</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="date" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>Tanggal Kegiatan</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="location" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>Lokasi (WFO/WFH)</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="description" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>Deskripsi Aktivitas</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="notes" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>Catatan / Hasil</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="status" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>Status Approval</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" value="supervisor" x-model="columns" class="rounded border-slate-350 text-[#0B63E5] focus:ring-blue-500 w-4 h-4">
                            <span>Nama Pembimbing</span>
                        </label>
                    </div>
                </div>

                <!-- Export Action -->
                <div class="pt-5 border-t border-slate-50 flex justify-between items-center">
                    <button @click="step = 2" type="button" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-extrabold flex items-center space-x-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Kembali</span>
                    </button>

                    <button @click="triggerDownload()" type="button"
                            class="px-8 py-3 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-xl transition font-black flex items-center space-x-2 shadow-sm text-sm">
                        <i class="fa-solid fa-circle-down"></i>
                        <span>Ekspor & Unduh Berkas</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
