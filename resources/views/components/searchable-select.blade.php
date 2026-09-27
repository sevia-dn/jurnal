@props([
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Cari atau pilih data',
    'id' => null,
    'required' => false,
    'onchange' => null,
])

@php
    $normalizedOptions = collect($options)->map(function ($option, $key): array {
        if (is_array($option)) {
            return [
                'value' => (string) ($option['value'] ?? $key),
                'label' => (string) ($option['label'] ?? $option['value'] ?? $key),
            ];
        }

        return ['value' => (string) $key, 'label' => (string) $option];
    })->values();
    $selectedValue = filled($selected) ? (string) $selected : '';
    $selectedLabel = data_get($normalizedOptions->firstWhere('value', $selectedValue), 'label', '');
@endphp

<div x-data="searchableSelect(@js($selectedValue), @js($selectedLabel), @js($normalizedOptions))" @searchable-select-value.window="setExternalValue($event.detail)" @click.outside="open = false" class="relative">
    <input x-ref="value" type="hidden" name="{{ $name }}" x-model="selected" @if($id) id="{{ $id }}" @endif @if($required) required @endif @if($onchange) onchange="{{ $onchange }}" @endif>
    <div class="relative">
        <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input x-model="query" @focus="open = true" @input="selected = ''; open = true" type="search" autocomplete="off" placeholder="{{ $placeholder }}" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-8 text-xs text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
        <button x-cloak x-show="query" type="button" @click="clear()" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-700" aria-label="Kosongkan pilihan"><i class="bi bi-x-circle"></i></button>
    </div>
    <div x-cloak x-show="open" x-transition class="absolute z-40 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-1 shadow-lg">
        <button type="button" @click="clear()" class="w-full rounded-md px-3 py-2 text-left text-xs text-slate-500 transition hover:bg-slate-50">Tidak memilih</button>
        <template x-for="option in filteredOptions()" :key="option.value">
            <button type="button" @click="choose(option)" class="block w-full rounded-md px-3 py-2 text-left text-xs text-slate-700 transition hover:bg-emerald-50" x-text="option.label"></button>
        </template>
        <p x-show="filteredOptions().length === 0" class="px-3 py-2 text-xs text-slate-400">Data tidak ditemukan.</p>
    </div>
</div>
