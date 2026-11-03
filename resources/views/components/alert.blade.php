@props([
    'type' => 'success', // 'success', 'error', 'warning', 'info'
    'title' => null
])

@php
$config = match($type) {
    'success' => [
        'bg' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
        'iconBg' => 'bg-emerald-100 text-emerald-700',
        'icon' => 'fa-solid fa-check',
        'defaultTitle' => 'Berhasil!'
    ],
    'error' => [
        'bg' => 'bg-rose-50 border-rose-200 text-rose-900',
        'iconBg' => 'bg-rose-100 text-rose-700',
        'icon' => 'fa-solid fa-triangle-exclamation',
        'defaultTitle' => 'Terjadi Kesalahan!'
    ],
    'warning' => [
        'bg' => 'bg-amber-50 border-amber-200 text-amber-900',
        'iconBg' => 'bg-amber-100 text-amber-700',
        'icon' => 'fa-solid fa-exclamation',
        'defaultTitle' => 'Peringatan!'
    ],
    default => [
        'bg' => 'bg-blue-50 border-blue-200 text-blue-900',
        'iconBg' => 'bg-blue-100 text-[#005B9C]',
        'icon' => 'fa-solid fa-circle-info',
        'defaultTitle' => 'Informasi'
    ]
};
@endphp

<div {{ $attributes->merge(['class' => "p-4.5 rounded-2xl border flex items-start space-x-3.5 shadow-2xs {$config['bg']}" ]) }}>
    <div class="w-8 h-8 rounded-xl {{ $config['iconBg'] }} flex items-center justify-center text-sm font-bold shrink-0">
        <i class="{{ $config['icon'] }}"></i>
    </div>
    <div class="flex-1">
        <h4 class="font-extrabold text-sm leading-snug">{{ $title ?? $config['defaultTitle'] }}</h4>
        <div class="text-xs font-medium mt-1 leading-relaxed opacity-90">
            {{ $slot }}
        </div>
    </div>
</div>
