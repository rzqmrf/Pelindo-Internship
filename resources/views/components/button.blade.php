@props([
    'variant' => 'primary', // 'primary', 'navy', 'secondary', 'success', 'danger', 'outline', 'ghost'
    'size' => 'md', // 'sm', 'md', 'lg'
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
$variantClasses = [
    'primary' => 'bg-[#005B9C] hover:bg-[#002B49] text-white font-extrabold border border-transparent shadow-xs',
    'navy' => 'bg-[#002B49] hover:bg-slate-900 text-white font-extrabold border border-transparent shadow-xs',
    'secondary' => 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold border border-slate-200/60',
    'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold border border-transparent shadow-xs',
    'danger' => 'bg-rose-500 hover:bg-rose-600 text-white font-extrabold border border-transparent shadow-xs',
    'outline' => 'bg-white hover:bg-slate-50 text-slate-700 font-extrabold border border-slate-300 shadow-2xs',
    'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-600 font-bold border border-transparent',
];

$sizeClasses = [
    'sm' => 'px-3.5 py-2 text-xs rounded-xl gap-1.5',
    'md' => 'px-5 py-2.5 text-sm rounded-xl gap-2',
    'lg' => 'px-6 py-3 text-base rounded-2xl gap-2.5',
];

$classes = 'inline-flex items-center justify-center transition-all cursor-pointer select-none ' . 
           ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . 
           ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon && $iconPosition === 'left')
        <i class="{{ $icon }}"></i>
    @endif
    
    <span>{{ $slot }}</span>

    @if($icon && $iconPosition === 'right')
        <i class="{{ $icon }}"></i>
    @endif
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon && $iconPosition === 'left')
        <i class="{{ $icon }}"></i>
    @endif

    <span>{{ $slot }}</span>

    @if($icon && $iconPosition === 'right')
        <i class="{{ $icon }}"></i>
    @endif
</button>
@endif
