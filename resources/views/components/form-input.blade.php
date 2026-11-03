@props([
    'type' => 'text',
    'name' => '',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'icon' => null,
    'required' => false
])

<div class="space-y-1.5">
    @if($label)
    <label for="{{ $name }}" class="block font-bold text-slate-800 text-sm">
        {{ $label }}
        @if($required) <span class="text-rose-500">*</span> @endif
    </label>
    @endif

    <div class="relative">
        @if($icon)
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
            <i class="{{ $icon }} text-sm"></i>
        </span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full text-sm font-semibold text-slate-900 bg-slate-50/80 border border-slate-300 rounded-xl p-3.5 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#005B9C]/20 focus:border-[#005B9C] ' . ($icon ? 'pl-11' : 'px-4')
            ]) }}>
    </div>

    @error($name)
    <p class="text-xs font-bold text-rose-600 mt-1 flex items-center gap-1">
        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
        <span>{{ $message }}</span>
    </p>
    @enderror
</div>
