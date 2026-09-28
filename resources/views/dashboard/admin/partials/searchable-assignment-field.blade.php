<div x-data="searchableSelect(@js($selectedId), @js($selectedLabel), @js($options))" @click.outside="open = false" class="relative">
    <label class="mb-1 block text-[11px] font-semibold text-slate-500">{{ $label }}</label>
    <input type="hidden" name="{{ $fieldName }}" x-model="selected">
    <div class="relative">
        <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input x-model="query" @focus="open = true; $event.target.select()" @input="selected = ''; open = true" type="search" autocomplete="off" placeholder="Cari guru atau NIP" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-8 text-xs text-slate-700 outline-none transition focus:border-[#155d50] focus:ring-2 focus:ring-[#155d50]/10">
        <button x-show="query" x-cloak type="button" @click="clear()" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700"><i class="bi bi-x-circle"></i></button>
    </div>
    <div x-cloak x-show="open" x-transition class="absolute z-30 mt-1 max-h-52 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-1 shadow-lg">
        <button type="button" @click="clear()" class="w-full rounded-md px-3 py-2 text-left text-xs text-slate-500 hover:bg-slate-50">Belum ditugaskan</button>
        <template x-for="option in filteredOptions()" :key="option.value">
            <button type="button" @click="choose(option)" class="block w-full rounded-md px-3 py-2 text-left text-xs text-slate-700 hover:bg-emerald-50" x-text="option.label"></button>
        </template>
        <p x-show="filteredOptions().length === 0" class="px-3 py-2 text-xs text-slate-400">Guru tidak ditemukan.</p>
    </div>
</div>
