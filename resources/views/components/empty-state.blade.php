@props([
    'title' => 'Data Tidak Ditemukan',
    'subtitle' => 'Belum ada informasi yang dapat ditampilkan saat ini.',
    'icon' => 'fa-regular fa-folder-open'
])

<div {{ $attributes->merge(['class' => 'py-12 px-6 text-center space-y-3']) }}>
    <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-3xl flex items-center justify-center text-2xl mx-auto shadow-inner">
        <i class="{{ $icon }}"></i>
    </div>
    <div class="space-y-1 max-w-sm mx-auto">
        <h4 class="text-base font-extrabold text-slate-800">{{ $title }}</h4>
        <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ $subtitle }}</p>
    </div>

    @if($slot->isNotEmpty())
    <div class="pt-2">
        {{ $slot }}
    </div>
    @endif
</div>
