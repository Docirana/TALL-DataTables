<div class="m-2">
    <div class="row">
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        @if (! empty($title))
                            <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    {{ $title }}
                                </h3>
                            </div>
                        @endif
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50 align-top">
                            <tr>
                                @foreach($headerData as $column)
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="@if($column->filterable) pb-2 @endif">
                                            @if ($column->sortable)
                                                <a wire:click.prevent="sortByDataKey('{{ $column->key }}')" class="flex items-center" role="button" href="#">
                                                    {{ $column->label }}
                                                    @if($sortField !== $column->key)
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="#000" viewBox="0 0 192 192" class="h-4 w-4 mr-2">
                                                            <path fill-rule="evenodd" d="M47.9999 192c-1.0609 0-2.0783-.421-2.8284-1.172l-43.99993-44c-1.562095-1.562-1.562095-4.094.00001-5.656 1.56209-1.563 4.09476-1.563 5.65685 0l37.17147 37.171V40c0-2.2091 1.7909-4 4-4 2.2092 0 4 1.7909 4 4v138.343l37.1717-37.171c1.5621-1.563 4.0947-1.563 5.6568 0 1.5621 1.562 1.5621 4.094 0 5.656l-44.0001 44c-.7501.751-1.7675 1.172-2.8284 1.172zM144 0c1.061 9.5e-7 2.078.421429 2.828 1.17158l44 44.00002c1.562 1.5621 1.562 4.0947 0 5.6568s-4.094 1.5621-5.656 0L148 13.6569V152c0 2.209-1.791 4-4 4s-4-1.791-4-4V13.6568l-37.172 37.1716c-1.562 1.5621-4.0944 1.5621-5.6565 0s-1.5621-4.0947 0-5.6568L141.172 1.17157C141.922.421425 142.939-9.5e-7 144 0z"/>
                                                        </svg>
                                                    @elseif($sortAsc)
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="#000" viewBox="0 0 192 192" class="h-4 w-4 mr-2">
                                                            <path fill-rule="evenodd" d="M96 146c-1.0609 0-2.0783-.421-2.8284-1.172L1.17158 52.8284c-1.562106-1.5621-1.562106-4.0947 0-5.6568 1.56209-1.5621 4.09476-1.5621 5.65684 0L96 136.343l89.172-89.1714c1.562-1.5621 4.094-1.5621 5.656 0 1.563 1.5621 1.563 4.0947 0 5.6568L98.8284 144.828C98.0783 145.579 97.0609 146 96 146z"/>
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="#000" viewBox="0 0 192 192" class="h-4 w-4 mr-2">
                                                            <path fill-rule="evenodd" d="M96 46c1.0609 0 2.0783.4214 2.8284 1.1716l91.9996 92.0004c1.563 1.562 1.563 4.094 0 5.656-1.562 1.563-4.094 1.563-5.656 0L96 55.6569 6.82843 144.828c-1.5621 1.563-4.09476 1.563-5.65686 0-1.562094-1.562-1.562094-4.094 0-5.656L93.1716 47.1716C93.9217 46.4214 94.9391 46 96 46z"/>
                                                        </svg>
                                                    @endif
                                                </a>
                                            @else
                                                {{ $column->label }}
                                            @endif
                                        </div>
                                        @if ($column->filterable)
                                            {{ View::make('tall-datatables::partials.' . $column->viewFile, ['column' => $column, 'value' => data_get($filters, $column->key)]) }}
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody wire:loading.class.delay="opacity-50">
                            @forelse($dataObjects as $row)
                                <tr class="@if ($loop->odd) bg-white @else bg-gray-50 @endif">
                                    @foreach($headerData as $column)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ data_get($row, $column->key, '') }}
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $headerData->count() }}">
                                        <div class="flex justify-center items-center">
                                            <span class="font-medium py-8 text-cool-gray-400 text-xl">
                                                No results found
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        @if (method_exists($dataObjects, 'links'))
                            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                                <div class="flex-auto flex">
                                    @if (null !== $perPage)
                                        <label>
                                            <select wire:model="perPage" class="form-control border border-gray-300 rounded-md text-sm font-medium">
                                                @foreach($perPageOptions as $perPageOption)
                                                    <option value="{{ $perPageOption }}">{{ $perPageOption }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @endif
                                </div>
                                <div class="flex flex-auto justify-end">
                                    {{ $dataObjects->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
