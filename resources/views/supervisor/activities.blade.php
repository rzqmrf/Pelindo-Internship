@extends('layouts.app')

@section('page_title', 'Approval Logbook Aktivitas Harian')

@section('content')
<div class="space-y-6" x-data="{ reviewModalOpen: false, selectedActivity: null, statusAction: 'approved', supervisorNote: '' }">

    <!-- Centralized Page Header -->
    <x-page-header 
        title="Persetujuan Logbook Aktivitas Harian" 
        subtitle="Periksa, setujui, minta revisi, atau tolak laporan aktivitas harian mahasiswa/siswa bimbingan Anda." 
        icon="fa-solid fa-clipboard-list"
    />

    <!-- 5 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Laporan -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Laporan</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalActivities }}</h3>
                <p class="text-[10px] font-extrabold text-[#0B63E5] mt-1">
                    <span>Laporan masuk</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-50/80 text-[#0B63E5] flex items-center justify-center text-base shrink-0">
                <i class="fa-regular fa-file-lines"></i>
            </div>
        </div>

        <!-- Card 2: Pending -->
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

        <!-- Card 3: Approved -->
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

        <!-- Card 4: Revision -->
        <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Revision</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $revisionCount }}</h3>
                <p class="text-[10px] font-extrabold text-purple-650">
                    <span>{{ $pctRevision }}% <span class="text-slate-400 font-bold ml-0.5">dari total</span></span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-purple-50/80 text-purple-655 flex items-center justify-center text-base shrink-0">
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

    <!-- Filters Bar Card -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('supervisor.activities') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end text-xs font-bold text-slate-700">
            <div>
                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-wider mb-1.5">Status Approval</label>
                <div class="relative">
                    <select name="status" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        <option value="">Semua Status Approval</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending (Menunggu Review)</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved (Disetujui)</option>
                        <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revision (Diminta Revisi)</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-wider mb-1.5">Peserta Bimbingan</label>
                <div class="relative">
                    <select name="intern_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        <option value="">Semua Peserta</option>
                        @foreach($myInterns as $intern)
                            <option value="{{ $intern->id }}" {{ request('intern_id') == $intern->id ? 'selected' : '' }}>{{ $intern->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-wider mb-1.5">Lokasi Kerja / Divisi</label>
                <div class="relative">
                    <select name="work_location" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-650 outline-none appearance-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        <option value="">Semua Lokasi Kerja</option>
                        <option value="WFO" {{ request('work_location') == 'WFO' ? 'selected' : '' }}>WFO (Work From Office)</option>
                        <option value="WFH" {{ request('work_location') == 'WFH' ? 'selected' : '' }}>WFH (Work From Home)</option>
                        <option value="Leave" {{ request('work_location') == 'Leave' ? 'selected' : '' }}>Izin / Sakit</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-wider mb-1.5">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-850 outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white rounded-2xl text-xs font-extrabold transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('supervisor.activities') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl text-xs font-extrabold hover:bg-slate-50 transition flex items-center justify-center space-x-1.5 shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Activities Review Table Card -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Tanggal</th>
                        <th class="py-3.5 px-4">Peserta Magang</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">Deskripsi Aktivitas</th>
                        <th class="py-3.5 px-4">Status Approval</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                    @forelse($activities as $act)
                        @php
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

                            <!-- Column 2: Peserta Magang -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-xs shadow-2xs shrink-0 {{ $avatarClass }}">
                                        {{ $firstLetter }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-800 text-sm leading-tight">{{ $act->intern?->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $act->intern?->internProfile?->institution }}</p>
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

                            <!-- Column 4: Deskripsi Aktivitas -->
                            <td class="py-3.5 px-4 max-w-xs md:max-w-md">
                                <p class="text-slate-800 font-semibold leading-relaxed line-clamp-2 text-xs">{{ $act->activity_description }}</p>
                                <span class="inline-flex px-2 py-0.5 bg-blue-50 text-[#0B63E5] border border-blue-100 rounded-lg text-[9px] font-extrabold mt-1.5 uppercase tracking-wider">
                                    {{ $category }}
                                </span>
                                @if($act->supervisor_note)
                                    <div class="p-2 bg-amber-50 border border-amber-100 rounded-xl text-[10px] text-amber-900 font-semibold mt-1.5 max-w-xs">
                                        Catatan Anda: {{ $act->supervisor_note }}
                                    </div>
                                @endif
                            </td>

                            <!-- Column 5: Status Approval -->
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

                            <!-- Column 6: Aksi -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                @if($act->status === 'pending')
                                    <button @click="selectedActivity = {{ json_encode($act) }}; statusAction = 'approved'; supervisorNote = selectedActivity.supervisor_note || ''; reviewModalOpen = true"
                                            class="px-4 py-2.5 bg-[#0B63E5] hover:bg-blue-750 text-white font-extrabold rounded-2xl text-xs flex items-center transition shadow-2xs gap-1.5 ml-auto">
                                        <i class="fa-solid fa-list-check text-xs"></i>
                                        <span>Review & Approve</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] ml-0.5"></i>
                                    </button>
                                @else
                                    <button @click="selectedActivity = {{ json_encode($act) }}; statusAction = '{{ $act->status }}'; supervisorNote = selectedActivity.supervisor_note || ''; reviewModalOpen = true"
                                            class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold rounded-2xl text-xs flex items-center transition shadow-2xs gap-1.5 ml-auto">
                                        <span>Lihat Detail</span>
                                        <i class="fa-solid fa-chevron-down text-[10px] ml-0.5"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-semibold text-xs">Tidak ada laporan aktivitas ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/20 flex justify-between items-center text-xs font-bold text-slate-500">
                <span>Menampilkan {{ $activities->firstItem() ?? 0 }} - {{ $activities->lastItem() ?? 0 }} dari {{ $activities->total() }} data</span>
                <div>{{ $activities->links() }}</div>
            </div>
        @endif
    </div>

    <!-- Review & Approval Modal -->
    <div x-show="reviewModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <template x-if="selectedActivity">
            <div @click.outside="reviewModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-black text-slate-800">Tinjau Laporan Aktivitas</h3>
                    <button @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-650"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <div class="bg-slate-50 border border-slate-200/50 p-4 rounded-3xl space-y-2.5 text-xs font-bold text-slate-700">
                    <div class="flex justify-between items-center">
                        <span class="font-black text-slate-800 text-sm" x-text="selectedActivity.intern ? selectedActivity.intern.name : ''"></span>
                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase border border-blue-100 bg-blue-50 text-[#0B63E5]" x-text="selectedActivity.work_location"></span>
                    </div>
                    <p class="text-slate-400 font-semibold" x-text="'Tanggal: ' + (selectedActivity.date ? new Date(selectedActivity.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : '')"></p>
                    <div class="pt-2 border-t border-slate-200/60">
                        <p class="font-black text-slate-700 text-xs mb-1">Deskripsi Kegiatan:</p>
                        <p class="text-slate-800 leading-relaxed font-semibold" x-text="selectedActivity.activity_description"></p>
                    </div>
                    <div x-show="selectedActivity.notes" class="pt-2">
                        <p class="font-black text-slate-700 text-xs mb-0.5">Catatan / Hasil:</p>
                        <p class="text-slate-600 italic font-semibold" x-text="selectedActivity.notes"></p>
                    </div>
                </div>

                <!-- Approval Form -->
                <form :action="'{{ url('/supervisor/activities') }}/' + selectedActivity.id + '/approve'" method="POST" class="space-y-4 text-xs font-bold text-slate-700">
                    @csrf
                    
                    <div>
                        <label class="block font-black text-slate-800 mb-2">Keputusan Approval *</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center justify-center p-3.5 rounded-2xl border cursor-pointer font-black transition select-none text-xs"
                                   :class="statusAction === 'approved' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 text-slate-750 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="status" value="approved" x-model="statusAction" class="sr-only">
                                <i class="fa-solid fa-circle-check mr-1.5"></i> Setujui
                            </label>

                            <label class="flex items-center justify-center p-3.5 rounded-2xl border cursor-pointer font-black transition select-none text-xs"
                                   :class="statusAction === 'revision' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-slate-50 text-slate-750 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="status" value="revision" x-model="statusAction" class="sr-only">
                                <i class="fa-solid fa-rotate mr-1.5"></i> Revisi
                            </label>

                            <label class="flex items-center justify-center p-3.5 rounded-2xl border cursor-pointer font-black transition select-none text-xs"
                                   :class="statusAction === 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-slate-50 text-slate-755 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="status" value="rejected" x-model="statusAction" class="sr-only">
                                <i class="fa-solid fa-xmark mr-1.5"></i> Tolak
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-black text-slate-800 mb-1.5">
                            Catatan Pembimbing <span x-show="statusAction !== 'approved'" class="text-rose-500">* (Wajib jika Revisi/Tolak)</span>
                        </label>
                        <textarea name="supervisor_note" x-model="supervisorNote" rows="3" 
                                  placeholder="Berikan masukan, umpan balik, atau instruksi revisi untuk peserta magang..."
                                  class="w-full p-3.5 border rounded-2xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-600 outline-none text-xs font-semibold leading-relaxed transition"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" @click="reviewModalOpen = false" class="px-5 py-2.5 bg-slate-100 text-slate-650 hover:bg-slate-200 rounded-xl font-extrabold text-xs transition">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-[#0B63E5] hover:bg-blue-755 text-white font-extrabold rounded-xl shadow-xs transition text-xs">
                            Simpan Keputusan
                        </button>
                    </div>
                </form>
            </div>
        </template>
    </div>

</div>
@endsection
