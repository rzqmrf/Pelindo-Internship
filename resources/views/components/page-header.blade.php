@props([
    'title' => '',
    'subtitle' => '',
    'icon' => null,
    'badge' => null,
    'action' => null
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
    <div class="space-y-1">
        <div class="flex items-center gap-3 flex-wrap">
            @if($icon)
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#005B9C] flex items-center justify-center text-lg shrink-0">
                    <i class="{{ $icon }}"></i>
                </div>
            @endif
            <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">{{ $title }}</h1>
            @if($badge)
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $badge }}
                </span>
            @endif
        </div>
        @if($subtitle)
            <p class="text-sm font-medium text-slate-600 leading-relaxed mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    @if(isset($action) && $action)
        <div class="flex items-center gap-3 shrink-0">
            {{ $action }}
        </div>
    @elseif($attributes->has('action-route'))
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ $attributes->get('action-route') }}" class="px-5 py-2.5 bg-[#005B9C] hover:bg-[#002B49] text-white font-bold rounded-xl text-sm transition shadow-sm flex items-center gap-2">
                @if($attributes->has('action-icon'))
                    <i class="{{ $attributes->get('action-icon') }} text-sm"></i>
                @endif
                <span>{{ $attributes->get('action-label', 'Aksi') }}</span>
            </a>
        </div>
    @endif
</div>
