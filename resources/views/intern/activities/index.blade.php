@extends('layouts.app')

@section('page_title', 'Riwayat Logbook Aktivitas Harian')

@section('content')
<div class="space-y-6" x-data="{ detailModalOpen: false, selectedActivity: null }">

    <!-- Centralized Page Header -->
    <x-page-header
        title="Riwayat & Status Approval Aktivitas Harian"
        subtitle="Daftar lengkap laporan harian yang telah dikirim beserta catatan dari pembimbing lapangan."
        icon="fa-regular fa-file-lines">
        <x-slot:action>
            <a href="{{ route('intern.activities.create') }}" class="px-4 py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs transition shadow-xs flex items-center space-x-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Isi Aktivitas Hari Ini</span>
            </a>
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-extrabold rounded-2xl text-xs transition border border-slate-200 shadow-2xs flex items-center space-x-2">
                <i class="fa-solid fa-file-pdf text-rose-500"></i>
                <span>Cetak Laporan PDF</span>
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Filter Bar Card matching screenshot -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('intern.activities.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end text-xs">
            <!-- Filter 1: Status Approval -->
            <div>
                <label class="block font-bold text-slate-700 text-[11px] mb-1.5">Status Approval</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revision</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <!-- Filter 2: Lokasi Kerja / Status -->
            <div>
                <label class="block font-bold text-slate-700 text-[11px] mb-1.5">Lokasi Kerja</label>
                <select name="work_location" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">Semua Lokasi</option>
                    <option value="WFO" {{ request('work_location') == 'WFO' ? 'selected' : '' }}>WFO (Work From Office)</option>
                    <option value="WFH" {{ request('work_location') == 'WFH' ? 'selected' : '' }}>WFH (Work From Home)</option>
                    <option value="Leave" {{ request('work_location') == 'Leave' ? 'selected' : '' }}>Izin</option>
                </select>
            </div>

            <!-- Filter 3: Tanggal Range -->
            <div>
                <label class="block font-bold text-slate-700 text-[11px] mb-1.5">Tanggal</label>
                <input type="month" name="month" value="{{ request('month') }}"
                    class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- Filter Action Buttons right -->
            <div class="lg:col-span-2 flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#002B49] hover:bg-[#001E34] text-white font-extrabold rounded-xl text-xs transition shadow-2xs flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('intern.activities.index') }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 font-extrabold rounded-xl text-xs transition border border-slate-200 shadow-2xs flex items-center space-x-1.5">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Main Activities Logbook Table Card matching screenshot -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-5">TANGGAL <i class="fa-solid fa-sort text-[9px] ml-1"></i></th>
                        <th class="py-4 px-5">LOKASI</th>
                        <th class="py-4 px-5">DESKRIPSI AKTIVITAS</th>
                        <th class="py-4 px-5">STATUS APPROVAL</th>
                        <th class="py-4 px-5">CATATAN PEMBIMBING</th>
                        <th class="py-4 px-5 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($activities as $act)
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Tanggal Column with Dot -->
                        <td class="py-4 px-5 font-extrabold text-slate-800 whitespace-nowrap">
                            <div class="flex items-center space-x-2.5">
                                <span class="w-2 h-2 rounded-full {{ $act->status === 'approved' ? 'bg-emerald-500' : ($act->status === 'pending' ? 'bg-amber-500' : ($act->status === 'revision' ? 'bg-blue-500' : 'bg-rose-500')) }}"></span>
                                <div>
                                    <p class="font-extrabold text-slate-800 text-xs">{{ $act->date->format('d M Y') }}</p>
                                    <p class="text-[10px] text-slate-400 font-semibold">{{ $act->date->isoFormat('dddd') }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Lokasi Column -->
                        <td class="py-4 px-5 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase {{ $act->work_location === 'WFO' ? 'bg-blue-100/90 text-blue-800' : ($act->work_location === 'WFH' ? 'bg-purple-100/90 text-purple-800' : 'bg-amber-100/90 text-amber-800') }}">
                                {{ $act->work_location }}
                            </span>
                        </td>

                        <!-- Deskripsi Aktivitas Column -->
                        <td class="py-4 px-5 max-w-sm">
                            <p class="text-slate-700 font-semibold leading-relaxed line-clamp-2">{{ $act->activity_description }}</p>
                        </td>

                        <!-- Status Approval Column -->
                        <td class="py-4 px-5 whitespace-nowrap">
                            @php
                            $badge = match($act->status) {
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'revision' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                            $icon = match($act->status) {
                            'approved' => 'fa-check',
                            'pending' => 'fa-clock',
                            'revision' => 'fa-rotate',
                            'rejected' => 'fa-xmark',
                            default => 'fa-bookmark',
                            };
                            @endphp
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold border uppercase tracking-wider {{ $badge }}">
                                <i class="fa-solid {{ $icon }} text-[9px]"></i>
                                <span>{{ strtoupper($act->status) }}</span>
                            </span>
                        </td>

                        <!-- Catatan Pembimbing Column -->
                        <td class="py-4 px-5 max-w-xs">
                            @if($act->supervisor_note)
                            @php
                            $noteStyle = match($act->status) {
                            'approved' => 'bg-emerald-50 text-emerald-900 border-emerald-200',
                            'revision' => 'bg-amber-50 text-amber-900 border-amber-200',
                            'rejected' => 'bg-rose-50 text-rose-900 border-rose-200',
                            default => 'bg-slate-50 text-slate-800 border-slate-200',
                            };
                            @endphp
                            <div class="p-2.5 rounded-xl border text-[11px] font-semibold leading-snug {{ $noteStyle }}">
                                {{ $act->supervisor_note }}
                            </div>
                            @else
                            <span class="text-slate-300 font-semibold">-</span>
                            @endif
                        </td>

                        <!-- Aksi Column -->
                        <td class="py-4 px-5 text-right whitespace-nowrap space-x-1">
                            <button @click="selectedActivity = {{ json_encode($act) }}; detailModalOpen = true"
                                class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition border border-slate-200 inline-flex items-center space-x-1.5 shadow-2xs">
                                <i class="fa-regular fa-eye text-slate-400 text-xs"></i>
                                <span>Lihat Detail</span>
                            </button>

                            @if(in_array($act->status, ['draft', 'revision']))
                            <a href="{{ route('intern.activities.edit', $act->id) }}"
                                class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition inline-flex items-center space-x-1.5 shadow-2xs">
                                <i class="fa-regular fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-xs font-semibold">
                            Tidak ada riwayat aktivitas ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination matching screenshot footer -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs font-semibold text-slate-500">
            <p>Menampilkan {{ $activities->firstItem() ?? 0 }} - {{ $activities->lastItem() ?? 0 }} dari {{ $activities->total() }} data</p>
            <div>
                {{ $activities->links() }}
            </div>
        </div>
    </div>

    <!-- Detail Modal matching screenshot -->
    <div x-show="detailModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4" x-cloak>
        <template x-if="selectedActivity">
            <div @click.outside="detailModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-800">Detail Laporan Aktivitas</h3>
                    <button @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <div>
                            <p class="font-extrabold text-slate-800 text-xs" x-text="'Tanggal: ' + (selectedActivity.date ? selectedActivity.date.substring(0,10) : '')"></p>
                            <p class="text-slate-500 font-semibold mt-0.5" x-text="'Lokasi: ' + selectedActivity.work_location"></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                            :class="{
                                  'bg-emerald-50 text-emerald-700 border border-emerald-200': selectedActivity.status == 'approved',
                                  'bg-amber-50 text-amber-700 border border-amber-200': selectedActivity.status == 'pending',
                                  'bg-blue-50 text-blue-700 border border-blue-200': selectedActivity.status == 'revision',
                                  'bg-rose-50 text-rose-700 border border-rose-200': selectedActivity.status == 'rejected'
                              }"
                            x-text="selectedActivity.status">
                        </span>
                    </div>

                    <div>
                        <p class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px] mb-1">Deskripsi Rincian Kegiatan</p>
                        <div class="p-3.5 bg-slate-50/80 border border-slate-100 rounded-2xl text-slate-800 leading-relaxed font-semibold" x-text="selectedActivity.activity_description"></div>
                    </div>

                    <div x-show="selectedActivity.notes">
                        <p class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px] mb-1">Catatan / Hasil Pengerjaan</p>
                        <p class="text-slate-700 font-semibold p-3 bg-slate-50/50 rounded-xl" x-text="selectedActivity.notes"></p>
                    </div>

                    <div x-show="selectedActivity.supervisor_note">
                        <p class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px] mb-1">Catatan Pembimbing Lapangan</p>
                        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl text-amber-900 font-semibold" x-text="selectedActivity.supervisor_note"></div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-extrabold text-xs transition">Tutup</button>
                </div>
            </div>
        </template>
    </div>

</div>
@endsection