<div class="mt-1 bg-white border border-gray-300 rounded-md shadow-sm">
    <div class="flex">
        <label class="w-1/2 flex-1 min-w-0">
            <input type="number"
                   name="{{ $column->key }}-from"
                   class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full border-r rounded-none rounded-l-md bg-transparent focus:z-10 sm:text-sm border-gray-300"
                   step="{{ $column->inputStep }}"
                   @if (null !== $column->maximum) max="{{ $column->maximum }}" @endif
                   @if (null !== $column->minimum) min="{{ $column->minimum }}" @endif
                   wire:model.debounce.500ms="filters.{{ $column->key }}.0"
                   value="{{ data_get($value, 0) }}"
            >
        </label>
        <label class="flex-1 min-w-0">
            <input type="number"
                   name="{{ $column->key }}-to"
                   class="focus:ring-indigo-500 focus:border-indigo-500 relative block w-full rounded-none rounded-r-md bg-transparent focus:z-10 sm:text-sm border-gray-300"
                   step="{{ $column->inputStep }}"
                   @if (null !== $column->maximum) max="{{ $column->maximum }}" @endif
                   @if (null !== $column->minimum) min="{{ $column->minimum }}" @endif
                   wire:model.debounce.500ms="filters.{{ $column->key }}.1"
                   value="{{ data_get($value, 1) }}"
            >
        </label>
    </div>
</div>
