@extends('layouts.app')

@section('page_title', 'Monitoring Seluruh Peserta Magang')

@section('content')
<div class="space-y-6" x-data="{ detailModalOpen: false, selectedActivity: null, rekapModalOpen: false }">

    <!-- Centralized Page Header -->
    <x-page-header
        title="Monitoring Real-Time Aktivitas Magang"
        subtitle="Pantau seluruh pengerjaan tugas harian, lokasi kerja (WFO/WFH/WFA/Izin), dan status approval dari seluruh divisi Pelindo."
        icon="fa-solid fa-eye"
        badge="Live">
        <x-slot:action>
            <button @click="rekapModalOpen = true" class="py-2.5 px-4 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-2xl text-xs font-black transition flex items-center justify-center space-x-2 shadow-xs shrink-0">
                <i class="fa-solid fa-file-invoice text-xs"></i>
                <span>Unduh Rekap Divisi</span>
            </button>
        </x-slot:action>
    </x-page-header>

    <!-- 5 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Aktivitas Hari Ini -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Aktivitas Hari Ini</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $activitiesTodayCount }}</h3>
                <p class="text-[10px] font-extrabold text-emerald-600 flex items-center gap-0.5">
                    <span><i class="fa-solid fa-arrow-up"></i> {{ abs($pctDiff) }}%</span>
                    <span class="text-slate-400 font-bold ml-0.5">dari kemarin</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Card 2: Approved -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Approved</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $approvedCount }}</h3>
                <p class="text-[10px] font-extrabold text-emerald-600">
                    <span>{{ $pctApproved }}% <span class="text-slate-400 font-bold ml-0.5">dari total</span></span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- Card 3: Pending -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Pending</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $pendingCount }}</h3>
                <p class="text-[10px] font-extrabold text-orange-500">
                    <span>{{ $pctPending }}% <span class="text-slate-400 font-bold ml-0.5">dari total</span></span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-orange-50/80 text-orange-500 flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Card 4: Revision -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Revision</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $revisionCount }}</h3>
                <p class="text-[10px] font-extrabold text-purple-600">
                    <span>{{ $pctRevision }}% <span class="text-slate-400 font-bold ml-0.5">dari total</span></span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-purple-50/80 text-purple-600 flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-rotate"></i>
            </div>
        </div>

        <!-- Card 5: Rejected -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Rejected</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $rejectedCount }}</h3>
                <p class="text-[10px] font-extrabold text-rose-600">
                    <span>{{ $pctRejected }}% <span class="text-slate-400 font-bold ml-0.5">dari total</span></span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-rose-50/80 text-rose-600 flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('admin.monitoring') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
            <!-- Search field -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peserta / deskripsi kegiatan..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Division select -->
            <div class="relative">
                <select name="division_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ str_replace('Divisi ', '', $div->name) }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Status select -->
            <div class="relative">
                <select name="status" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Status Approval</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revision Needed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Location select -->
            <div class="relative">
                <select name="work_location" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    <option value="">Semua Lokasi Kerja</option>
                    <option value="WFO" {{ request('work_location') == 'WFO' ? 'selected' : '' }}>WFO (Work From Office)</option>
                    <option value="WFH" {{ request('work_location') == 'WFH' ? 'selected' : '' }}>WFH (Work From Home)</option>
                    <option value="Leave" {{ request('work_location') == 'Leave' ? 'selected' : '' }}>Izin / Sakit / Leave</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl text-xs font-extrabold transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.monitoring') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl text-xs font-extrabold hover:bg-slate-50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Activities Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Tanggal</th>
                        <th class="py-3.5 px-4">Peserta & Divisi</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">Deskripsi Kegiatan</th>
                        <th class="py-3.5 px-4">Pembimbing</th>
                        <th class="py-3.5 px-4">Status Approval</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @forelse($activities as $act)
                    @php
                    // Initials letter code
                    $firstLetter = strtoupper(substr($act->intern?->name ?? 'U', 0, 1));

                    $avatarClass = 'bg-blue-50 text-[#0B63E5]';
                    if (in_array($firstLetter, ['G','H','I','J','K','L','M'])) {
                    $avatarClass = 'bg-emerald-50 text-emerald-600';
                    } elseif (in_array($firstLetter, ['N','O','P','Q','R','S','T'])) {
                    $avatarClass = 'bg-purple-50 text-purple-605';
                    } elseif (in_array($firstLetter, ['U','V','W','X','Y','Z'])) {
                    $avatarClass = 'bg-orange-50 text-orange-500';
                    }

                    // Dynamic category mapping by description contents
                    $desc = strtolower($act->activity_description);
                    $category = 'Task Activity';
                    if (Str::contains($desc, ['api', 'webhook', 'backend', 'laravel', 'database', 'sql', 'query', 'redis'])) {
                    $category = 'Backend Development';
                    } elseif (Str::contains($desc, ['nginx', 'ssl', 'server', 'deploy', 'hosting', 'konfigurasi'])) {
                    $category = 'System Administration';
                    } elseif (Str::contains($desc, ['dashboard', 'chart.js', 'tampilan', 'frontend', 'css', 'html', 'alpine.js', 'design'])) {
                    $category = 'Frontend Development';
                    } elseif (Str::contains($desc, ['onboarding', 'absensi', 'rekrutmen', 'rekap', 'karyawan', 'pekerja'])) {
                    $category = 'HR Operations';
                    } elseif (Str::contains($desc, ['dokumen', 'administrasi', 'kontrak', 'arsip'])) {
                    $category = 'HR Administration';
                    } elseif (Str::contains($desc, ['rapat', 'koordinasi', 'meeting', 'diskusi'])) {
                    $category = 'Meeting';
                    } elseif (Str::contains($desc, ['studi', 'literatur', 'riset', 'belajar', 'keamanan', 'jwt', 'csrf'])) {
                    $category = 'Research & Study';
                    }
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <!-- Column 1: Tanggal -->
                        <td class="py-3.5 px-5 whitespace-nowrap">
                            <p class="text-base font-black text-slate-800 leading-none">{{ $act->date->format('d') }}</p>
                            <p class="text-[10px] text-slate-550 font-black uppercase mt-1">{{ strtoupper($act->date->isoFormat('MMM YYYY')) }}</p>
                            <p class="text-[9px] text-slate-400 font-semibold mt-0.5">{{ $act->created_at ? $act->created_at->format('H:i') . ' WIB' : '-' }}</p>
                        </td>

                        <!-- Column 2: Peserta & Divisi -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-xs shadow-2xs shrink-0 {{ $avatarClass }}">
                                    {{ $firstLetter }}
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-800 text-sm leading-tight">{{ $act->intern?->name }}</p>
                                    <p class="text-[11px] text-[#0B63E5] font-semibold mt-0.5">
                                        {{ $act->intern?->internProfile?->division ? str_replace('Divisi ', '', $act->intern->internProfile->division->name) : 'Belum Diplot' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Column 3: Lokasi -->
                        <td class="py-3.5 px-4">
                            @if($act->work_location === 'WFO')
                            <p class="text-[#0B63E5] font-black text-xs leading-none">WFO</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-1">Kantor Pusat</p>
                            @elseif($act->work_location === 'WFH')
                            <p class="text-purple-600 font-black text-xs leading-none">WFH</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-1">Remote</p>
                            @else
                            <p class="text-amber-600 font-black text-xs leading-none">{{ $act->work_location }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-1">Permit/Leave</p>
                            @endif
                        </td>

                        <!-- Column 4: Deskripsi Kegiatan -->
                        <td class="py-3.5 px-4 max-w-xs md:max-w-md">
                            <p class="text-slate-800 font-semibold leading-relaxed line-clamp-2 text-xs">{{ $act->activity_description }}</p>
                            <span class="inline-flex px-2 py-0.5 bg-blue-50 text-[#0B63E5] border border-blue-100 rounded-lg text-[9px] font-extrabold mt-1.5 uppercase tracking-wider">
                                {{ $category }}
                            </span>
                        </td>

                        <!-- Column 5: Pembimbing -->
                        <td class="py-3.5 px-4">
                            <p class="font-extrabold text-slate-800 leading-tight">{{ $act->supervisor?->name ?? 'Belum Ditentukan' }}</p>
                            <p class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $act->supervisor?->supervisorProfile?->position_title ?? 'Pembimbing' }}</p>
                        </td>

                        <!-- Column 6: Status Approval -->
                        <td class="py-3.5 px-4">
                            @php
                            $statusData = match($act->status) {
                            'approved' => ['bg-emerald-50 text-emerald-600 border-emerald-100', 'bg-emerald-500', 'APPROVED'],
                            'pending' => ['bg-amber-50 text-amber-600 border-amber-100', 'bg-amber-500', 'PENDING'],
                            'revision' => ['bg-blue-50 text-blue-600 border-blue-100', 'bg-blue-500', 'REVISION'],
                            default => ['bg-rose-50 text-rose-600 border-rose-100', 'bg-rose-500', 'REJECTED'],
                            };
                            @endphp
                            <div class="space-y-1">
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[9px] font-black border uppercase tracking-wider {{ $statusData[0] }}">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $statusData[1] }}"></span>
                                    <span>{{ $statusData[2] }}</span>
                                </span>
                                <p class="text-[10px] text-slate-400 font-semibold">
                                    {{ $act->updated_at ? $act->updated_at->format('d M Y, H:i') : '' }}
                                </p>
                            </div>
                        </td>

                        <!-- Column 7: Aksi -->
                        <td class="py-3.5 px-5 text-right">
                            <button @click="selectedActivity = {{ json_encode($act) }}; detailModalOpen = true"
                                class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-black rounded-xl text-xs flex items-center transition shadow-2xs justify-center gap-1.5">
                                <i class="fa-solid fa-eye text-slate-400"></i>
                                <span>Detail</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 font-semibold">Tidak ada log aktivitas harian yang sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/20">
            {{ $activities->links() }}
        </div>
        @endif
    </div>

    <!-- Detail Activity Modal -->
    <div x-show="detailModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <template x-if="selectedActivity">
            <div @click.outside="detailModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-800">Detail Aktivitas Harian</h3>
                    <button @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-650"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <div class="space-y-4 text-xs font-bold text-slate-700">
                    <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div>
                            <p class="font-extrabold text-slate-800 text-sm" x-text="selectedActivity.intern ? selectedActivity.intern.name : '-'"></p>
                            <p class="text-slate-400 font-semibold mt-0.5" x-text="selectedActivity.date ? new Date(selectedActivity.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : ''"></p>
                        </div>
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase border tracking-wider"
                            :class="{
                                  'bg-emerald-50 text-emerald-600 border-emerald-100': selectedActivity.status == 'approved',
                                  'bg-amber-50 text-amber-600 border-amber-100': selectedActivity.status == 'pending',
                                  'bg-blue-50 text-blue-600 border-blue-100': selectedActivity.status == 'revision',
                                  'bg-rose-50 text-rose-600 border-rose-100': selectedActivity.status == 'rejected'
                              }"
                            x-text="selectedActivity.status">
                        </span>
                    </div>

                    <div>
                        <p class="font-black text-slate-400 uppercase tracking-wider text-[10px] mb-1">Lokasi Kerja</p>
                        <p class="font-extrabold text-slate-700 text-xs" x-text="selectedActivity.work_location === 'WFO' ? 'WFO (Work From Office) - Kantor Pusat' : (selectedActivity.work_location === 'WFH' ? 'WFH (Work From Home) - Remote' : selectedActivity.work_location)"></p>
                    </div>

                    <div>
                        <p class="font-black text-slate-400 uppercase tracking-wider text-[10px] mb-1">Deskripsi Kegiatan</p>
                        <div class="p-3.5 bg-slate-50 rounded-xl text-slate-800 leading-relaxed font-semibold" x-text="selectedActivity.activity_description"></div>
                    </div>

                    <div>
                        <p class="font-black text-slate-400 uppercase tracking-wider text-[10px] mb-1">Catatan / Hasil Pengerjaan</p>
                        <p class="text-slate-650 font-semibold" x-text="selectedActivity.notes || 'Tidak ada catatan.'"></p>
                    </div>

                    <div x-show="selectedActivity.supervisor_note">
                        <p class="font-black text-slate-400 uppercase tracking-wider text-[10px] mb-1">Catatan Pembimbing Lapangan</p>
                        <div class="p-3.5 bg-amber-50 border border-amber-100 rounded-xl text-amber-900 font-semibold" x-text="selectedActivity.supervisor_note"></div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-extrabold transition">Tutup</button>
                </div>
            </div>
        </template>
    </div>

    <!-- Rekap Modal -->
    <div x-show="rekapModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        x-transition.opacity
        x-cloak>
        <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 border border-slate-100 shadow-xl" @click.away="rekapModalOpen = false">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-file-csv text-[#0B63E5]"></i>
                    <span>Ekspor Rekap Presensi Bulanan</span>
                </h3>
                <button @click="rekapModalOpen = false" class="text-slate-400 hover:text-slate-650 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.monitoring.export_rekap') }}" method="GET" class="space-y-4 text-xs font-semibold">

                <!-- Divisi Picker -->
                <div>
                    <label class="block font-extrabold text-slate-800 mb-2">Divisi / Unit Kerja</label>
                    <select name="division_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-850 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $div)
                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulan Picker -->
                <div>
                    <label class="block font-extrabold text-slate-800 mb-2">Pilih Bulan Rekap *</label>
                    <input type="month" name="month" value="{{ date('Y-m') }}" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-850 focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="rekapModalOpen = false"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-xl font-extrabold transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#0B63E5] hover:bg-blue-700 text-white rounded-xl font-extrabold transition shadow-2xs flex items-center space-x-1.5">
                        <i class="fa-solid fa-download"></i>
                        <span>Unduh CSV</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection