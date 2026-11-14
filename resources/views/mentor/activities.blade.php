@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Review Logbook Harian</h1>
        <p class="text-sm text-slate-500">Tinjau, beri catatan teknis, atau setujui aktivitas harian peserta bimbingan Anda.</p>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Peserta</label>
            <select name="intern_id" class="px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <option value="">Semua Peserta</option>
                @foreach($myInterns as $intern)
                    <option value="{{ $intern->id }}" {{ request('intern_id') == $intern->id ? 'selected' : '' }}>{{ $intern->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-1">Status</label>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <option value="">Semua Status</option>
                @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'revision' => 'Revisi', 'rejected' => 'Ditolak'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-[#005B9C] text-white rounded-xl text-sm font-bold">Filter</button>
    </form>

    <div class="space-y-4">
        @forelse($activities as $act)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                    <div>
                        <p class="font-extrabold text-slate-900">{{ $act->intern?->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500">{{ $act->date?->format('d M Y') }} &bull; {{ $act->work_location }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $act->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($act->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">{{ $act->status }}</span>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-line mb-4">{{ $act->activity_description }}</p>

                <form action="{{ route('mentor.activities.review', $act->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2 pt-3 border-t border-slate-100">
                    @csrf
                    <input type="text" name="supervisor_note" value="{{ $act->supervisor_note }}" placeholder="Catatan review mentor (wajib jika revisi/ditolak)..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    <button type="submit" name="status" value="approved" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold">Setujui</button>
                    <button type="submit" name="status" value="revision" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold">Minta Revisi</button>
                    <button type="submit" name="status" value="rejected" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold">Tolak</button>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-500">Belum ada logbook untuk direview.</div>
        @endforelse
        <div>{{ $activities->links() }}</div>
    </div>
</div>
@endsection
