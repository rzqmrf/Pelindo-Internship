@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Log Viewer -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/70 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-[#002B49] tracking-tight">Laravel Error &amp; Log Viewer</h1>
            <p class="text-xs sm:text-sm font-semibold text-slate-600">
                Membaca entri terbaru dari <code>storage/logs/laravel.log</code> (ukuran berkas saat ini: <strong>{{ $formattedSize }}</strong>).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            @if($fileExists && $fileSize > 0)
                <a href="{{ route('superadmin.logs.download') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-download text-[#005B9C]"></i>
                    <span>Unduh .log</span>
                </a>

                <form action="{{ route('superadmin.logs.clear') }}" method="POST"
                      onsubmit="return confirm('Kosongkan seluruh isi berkas laravel.log sekarang?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-800 font-extrabold text-xs transition flex items-center gap-2">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Bersihkan Log</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filter Level & Pencarian -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/70 shadow-xs space-y-4">
        <div class="flex flex-wrap gap-2">
            @php
                $levels = [
                    'ALL' => ['label' => 'Semua Entri', 'count' => $levelCounts['ALL'], 'active' => 'bg-[#002B49] text-white'],
                    'ERROR' => ['label' => 'Error / Critical', 'count' => $levelCounts['ERROR'], 'active' => 'bg-rose-600 text-white'],
                    'WARNING' => ['label' => 'Warning / Notice', 'count' => $levelCounts['WARNING'], 'active' => 'bg-amber-600 text-white'],
                    'INFO' => ['label' => 'Info / Debug', 'count' => $levelCounts['INFO'], 'active' => 'bg-[#005B9C] text-white'],
                ];
            @endphp
            @foreach($levels as $lvlKey => $meta)
                <a href="{{ route('superadmin.logs', array_filter(['level' => $lvlKey !== 'ALL' ? $lvlKey : null, 'search' => $search ?: null])) }}"
                   class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-2 {{ $selectedLevel === $lvlKey ? $meta['active'] : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <span>{{ $meta['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] {{ $selectedLevel === $lvlKey ? 'bg-white/20 text-white' : 'bg-white text-slate-700' }}">{{ $meta['count'] }}</span>
                </a>
            @endforeach
        </div>

        <form action="{{ route('superadmin.logs') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            @if($selectedLevel !== 'ALL')
                <input type="hidden" name="level" value="{{ $selectedLevel }}">
            @endif
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari pesan exception, nama controller, atau baris stack trace..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#005B9C]">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold text-xs transition">
                    Cari Log
                </button>
                @if($search !== '' || $selectedLevel !== 'ALL')
                    <a href="{{ route('superadmin.logs') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Daftar Entri Log -->
    <div class="space-y-3">
        @forelse($entries as $idx => $entry)
            @php
                $isError = in_array($entry['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'], true);
                $isWarn = in_array($entry['level'], ['WARNING', 'NOTICE'], true);
                $badgeClass = $isError
                    ? 'bg-rose-100 text-rose-800 border border-rose-200'
                    : ($isWarn ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-blue-50 text-[#005B9C] border border-blue-200');
            @endphp
            <div x-data="{ expanded: false }" class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-[11px]">
                            <span class="px-2 py-0.5 rounded font-extrabold {{ $badgeClass }}">{{ $entry['level'] }}</span>
                            <span class="font-mono font-semibold text-slate-500">{{ $entry['timestamp'] }}</span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold uppercase">{{ $entry['env'] }}</span>
                        </div>
                        <p class="text-xs sm:text-sm font-bold text-slate-900 break-words leading-relaxed">
                            {{ $entry['message'] }}
                        </p>
                    </div>

                    @if(!empty($entry['stack']))
                        <button type="button" @click="expanded = !expanded"
                                class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[11px] shrink-0 self-start">
                            <span x-text="expanded ? 'Sembunyikan Trace' : 'Lihat Stack Trace'"></span>
                        </button>
                    @endif
                </div>

                @if(!empty($entry['stack']))
                    <div x-show="expanded" x-cloak class="border-t border-slate-200 bg-slate-900 text-slate-100 p-4 overflow-x-auto">
                        <pre class="text-[11px] font-mono leading-relaxed whitespace-pre-wrap break-all">{{ $entry['stack'] }}</pre>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200/70 p-12 text-center space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto text-lg">
                    <i class="fa-solid fa-check"></i>
                </div>
                <p class="text-sm font-extrabold text-slate-800">
                    {{ !$fileExists || $fileSize === 0 ? 'Berkas laravel.log bersih (tidak ada error tercatat).' : 'Tidak ada entri log yang sesuai dengan filter saat ini.' }}
                </p>
                <p class="text-xs font-medium text-slate-500">
                    Jika terjadi exception atau gangguan runtime pada aplikasi, rincian baris kode dan waktu kejadian akan muncul di halaman ini.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
