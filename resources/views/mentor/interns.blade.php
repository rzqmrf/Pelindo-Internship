@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Peserta Bimbingan Mentor</h1>
            <p class="text-sm text-slate-500">Daftar peserta magang yang berada di bawah bimbingan teknis Anda.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIM / kampus..." class="px-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#005B9C]">
            <button type="submit" class="px-4 py-2 bg-[#005B9C] text-white rounded-xl text-sm font-bold">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Peserta</th>
                        <th class="px-6 py-4">Divisi</th>
                        <th class="px-6 py-4">Supervisor Unit</th>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($interns as $intern)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $intern->user?->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $intern->institution }} &bull; {{ $intern->student_id_number }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $intern->division?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $intern->supervisor?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-600">{{ $intern->start_date?->format('d M Y') }} - {{ $intern->end_date?->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 uppercase">{{ $intern->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada peserta magang yang ditugaskan kepada Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $interns->links() }}</div>
    </div>
</div>
@endsection
