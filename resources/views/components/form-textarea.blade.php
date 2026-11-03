@props([
    'name' => '',
    'label' => null,
    'placeholder' => '',
    'rows' => 4,
    'required' => false
])

<div class="space-y-1.5">
    @if($label)
    <label for="{{ $name }}" class="block font-bold text-slate-800 text-sm">
        {{ $label }}
        @if($required) <span class="text-rose-500">*</span> @endif
    </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full text-sm font-semibold text-slate-900 bg-slate-50/80 border border-slate-300 rounded-xl p-3.5 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#005B9C]/20 focus:border-[#005B9C] leading-relaxed'
        ]) }}>{{ $slot->isNotEmpty() ? $slot : old($name) }}</textarea>

    @error($name)
    <p class="text-xs font-bold text-rose-600 mt-1 flex items-center gap-1">
        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
        <span>{{ $message }}</span>
    </p>
    @enderror
</div>
