@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Pengajuan Izin Lintas Divisi (Admin HR)</h1>
            <p class="text-sm text-slate-500">Pantau seluruh pengajuan izin peserta magang dan lakukan override persetujuan jika pembimbing berhalangan.</p>
        </div>
        <div class="flex gap-2">
            @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
                <a href="{{ route('admin.leaves.index', ['status' => $key]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === $key ? 'bg-[#005B9C] text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Peserta &amp; Divisi</th>
                        <th class="px-6 py-4">Pembimbing</th>
                        <th class="px-6 py-4">Tanggal &amp; Jenis</th>
                        <th class="px-6 py-4">Alasan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi Override</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leaves as $leave)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $leave->intern?->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $leave->intern?->internProfile?->division?->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $leave->supervisor?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-800">{{ $leave->start_date?->format('d M Y') }} - {{ $leave->end_date?->format('d M Y') }}</p>
                                <span class="text-xs uppercase font-bold text-[#005B9C]">{{ $leave->type }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 max-w-xs">{{ $leave->reason }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $leave->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($leave->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $leave->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($leave->status === 'pending')
                                    <div class="inline-flex gap-1.5">
                                        <form action="{{ route('admin.leaves.approve', $leave->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-bold">Setujui</button>
                                        </form>
                                        <form action="{{ route('admin.leaves.reject', $leave->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-bold">Tolak</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500">Tidak ada data pengajuan izin untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $leaves->links() }}</div>
    </div>
</div>
@endsection
