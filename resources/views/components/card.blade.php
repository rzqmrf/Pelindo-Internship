@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'padding' => 'p-6',
    'headerAction' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden']) }}>
    @if($title || $subtitle || isset($headerAction))
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
        <div class="flex items-center space-x-3">
            @if($icon)
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#005B9C] flex items-center justify-center text-lg shrink-0">
                <i class="{{ $icon }}"></i>
            </div>
            @endif
            <div>
                @if($title)
                <h3 class="font-extrabold text-slate-900 text-base leading-snug">{{ $title }}</h3>
                @endif
                @if($subtitle)
                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if(isset($headerAction) && $headerAction)
        <div>
            {{ $headerAction }}
        </div>
        @endif
    </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
