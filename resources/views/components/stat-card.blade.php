@props([
    'title' => '',
    'value' => '0',
    'trend' => '',
    'trendType' => 'up', // 'up', 'down', 'neutral'
    'icon' => 'fa-solid fa-chart-line',
    'color' => 'blue', // 'blue', 'navy', 'emerald', 'amber', 'purple', 'indigo', 'rose'
    'href' => null
])

@php
$colorMap = [
    'blue' => [
        'iconBg' => 'bg-blue-50 text-[#005B9C]',
        'trendColor' => 'text-[#005B9C]'
    ],
    'navy' => [
        'iconBg' => 'bg-slate-100 text-[#002B49]',
        'trendColor' => 'text-[#002B49]'
    ],
    'emerald' => [
        'iconBg' => 'bg-emerald-50 text-emerald-600',
        'trendColor' => 'text-emerald-600'
    ],
    'amber' => [
        'iconBg' => 'bg-amber-50 text-amber-600',
        'trendColor' => 'text-amber-600'
    ],
    'purple' => [
        'iconBg' => 'bg-purple-50 text-purple-600',
        'trendColor' => 'text-purple-600'
    ],
    'indigo' => [
        'iconBg' => 'bg-indigo-50 text-indigo-600',
        'trendColor' => 'text-indigo-600'
    ],
    'rose' => [
        'iconBg' => 'bg-rose-50 text-rose-600',
        'trendColor' => 'text-rose-600'
    ]
];

$selectedColor = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<{{ $href ? 'a' : 'div' }} {{ $href ? "href={$href}" : '' }}
    {{ $attributes->merge(['class' => 'bg-white rounded-3xl p-6 border border-slate-100 shadow-xs flex items-center justify-between transition hover:shadow-md hover:border-slate-200 group']) }}>
    <div class="space-y-1.5">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $title }}</p>
        <h3 class="text-3xl font-black text-slate-900 tracking-tight leading-none">{{ $value }}</h3>
        
        @if($trend)
        <p class="text-xs font-bold {{ $selectedColor['trendColor'] }} flex items-center gap-1 pt-1">
            @if($trendType === 'up')
                <i class="fa-solid fa-arrow-up text-[10px]"></i>
            @elseif($trendType === 'down')
                <i class="fa-solid fa-arrow-down text-[10px]"></i>
            @endif
            <span>{{ $trend }}</span>
        </p>
        @endif
    </div>

    <div class="w-14 h-14 rounded-2xl {{ $selectedColor['iconBg'] }} flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform duration-200">
        <i class="{{ $icon }}"></i>
    </div>
</{{ $href ? 'a' : 'div' }}>
