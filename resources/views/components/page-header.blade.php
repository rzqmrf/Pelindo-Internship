@props([
    'title' => '',
    'subtitle' => '',
    'icon' => null,
    'badge' => null,
    'action' => null
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="space-y-1">
        <div class="flex items-center gap-2.5 flex-wrap">
            <h1 class="text-xl font-black text-slate-800 tracking-tight leading-none">{{ $title }}</h1>
            @if($badge)
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                    {{ $badge }}
                </span>
            @endif
        </div>
        @if($subtitle)
            <p class="text-xs font-semibold text-slate-400 leading-relaxed">{{ $subtitle }}</p>
        @endif
    </div>

    @if(isset($action) && $action)
        <div class="flex items-center gap-3 shrink-0">
            {{ $action }}
        </div>
    @elseif($attributes->has('action-route'))
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ $attributes->get('action-route') }}" class="px-5 py-2.5 bg-[#0B63E5] hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs transition shadow-sm flex items-center gap-2">
                @if($attributes->has('action-icon'))
                    <i class="{{ $attributes->get('action-icon') }} text-xs"></i>
                @endif
                <span>{{ $attributes->get('action-label', 'Aksi') }}</span>
            </a>
        </div>
    @endif
</div>
