@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Centralized Page Header -->
    <x-page-header 
        title="Detail & Track Record Peserta Magang" 
        subtitle="Profil lengkap biodata, divisi, pembimbing, riwayat presensi, dan klaim uang saku." 
        icon="fa-solid fa-user-gear"
    >
        <x-slot:action>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.interns.edit', $intern->id) }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl text-xs transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit Profil</span>
                </a>
                <a href="{{ route('admin.interns.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Card: Profile Snapshot -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-5 lg:col-span-1">
            <div class="text-center space-y-3 pb-5 border-b border-slate-100">
                <div class="w-20 h-20 rounded-full bg-blue-100 text-[#0B63E5] flex items-center justify-center font-black text-2xl mx-auto shadow-inner">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-400 font-medium">{{ $user->email }}</p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ strtoupper($intern->status) }}</span>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">NIM / NISM:</span>
                    <span class="font-bold text-slate-700 font-mono">{{ $intern->student_id_number }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">NIK:</span>
                    <span class="font-bold text-slate-700 font-mono">{{ $intern->nik_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Institusi / Kampus:</span>
                    <span class="font-bold text-slate-700 text-right">{{ $intern->institution }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Jurusan:</span>
                    <span class="font-bold text-slate-700 text-right">{{ $intern->major }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Agama:</span>
                    <span class="font-bold text-slate-700">{{ $intern->religion ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Jenis Kelamin:</span>
                    <span class="font-bold text-slate-700">{{ $intern->gender == 'L' ? 'Laki-laki' : ($intern->gender == 'P' ? 'Perempuan' : '-') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Divisi Penempatan:</span>
                    <span class="font-bold text-blue-600 text-right">{{ $intern->division?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-400 font-medium">Pembimbing Lapangan:</span>
                    <span class="font-bold text-slate-700 text-right">{{ $intern->supervisor?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-400 font-medium">Periode Magang:</span>
                    <span class="font-bold text-slate-700 text-right font-mono">{{ $intern->start_date?->format('d/m/Y') }} s.d {{ $intern->end_date?->format('d/m/Y') }}</span>
                </div>
            </div>

            <!-- Rekening Section -->
            <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 space-y-2 text-xs">
                <span class="font-extrabold text-[#0B63E5] block">Informasi Rekening Bank</span>
                <div class="flex justify-between">
                    <span class="text-slate-500">Bank:</span>
                    <span class="font-bold text-slate-800">{{ $intern->bank_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">No. Rekening:</span>
                    <span class="font-bold text-slate-800 font-mono">{{ $intern->bank_account_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Atas Nama:</span>
                    <span class="font-bold text-slate-800">{{ $intern->bank_account_holder ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Right Content: Track Record Tabs & Activity Logs -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Riwayat Presensi Terbaru -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600"></i>
                    <span>Riwayat Presensi Terbaru</span>
                </h3>

                @if($attendances->isEmpty())
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada catatan presensi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] border-b border-slate-100">
                                <tr>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Mode</th>
                                    <th class="p-3">Check-In</th>
                                    <th class="p-3">Check-Out</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                @foreach($attendances as $att)
                                    @php $badge = App\Helpers\StatusHelper::attendanceBadge($att->status); @endphp
                                    <tr>
                                        <td class="p-3 font-bold">{{ $att->date->format('d M Y') }}</td>
                                        <td class="p-3 font-semibold">{{ $att->work_location }}</td>
                                        <td class="p-3 font-mono">{{ $att->check_in_time ? substr($att->check_in_time, 0, 5) : '-' }}</td>
                                        <td class="p-3 font-mono">{{ $att->check_out_time ? substr($att->check_out_time, 0, 5) : '-' }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['class'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Logbook Harian Terbaru -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-file-signature text-purple-600"></i>
                    <span>Aktivitas Logbook Terbaru</span>
                </h3>

                @if($activities->isEmpty())
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada catatan logbook harian.</p>
                @else
                    <div class="space-y-3">
                        @foreach($activities as $act)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800">{{ $act->date->format('d M Y') }} ({{ $act->work_location }})</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
                                        {{ strtoupper($act->status) }}
                                    </span>
                                </div>
                                <p class="text-slate-600 leading-relaxed">{{ Str::limit($act->activity_description, 120) }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
