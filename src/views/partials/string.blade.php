<div>
    <label class="mt-1 grid grid-cols-1">
        <input
            type="text"
            name="{{ $column->key }}"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border border-gray-300 rounded-md placeholder-gray-400"
            wire:model.debounce.500ms="filters.{{ $column->key }}"
            value="{{ $value }}"
        >
    </label>
</div>
