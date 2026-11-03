@props([
    'name' => 'modal',
    'title' => null,
    'maxWidth' => 'md'
])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '4xl' => 'max-w-4xl',
    default => 'max-w-md'
};
@endphp

<div
    x-show="{{ $name }}"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
    x-cloak>
    <div
        @click.away="{{ $name }} = false"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white rounded-3xl {{ $maxWidthClass }} w-full p-6 shadow-2xl space-y-5 border border-slate-100">

        @if($title)
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-base font-extrabold text-slate-900">{{ $title }}</h3>
            <button @click="{{ $name }} = false" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        @endif

        <div>
            {{ $slot }}
        </div>
    </div>
</div>
