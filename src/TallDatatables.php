<?php

declare(strict_types=1);

namespace Docirana\TallDatatables;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithPagination;
use ReflectionMethod;

abstract class TallDatatables extends Component
{
    use WithPagination;

    public ?string $title;

    public ?string $sortField = null;

    public bool $sortAsc = true;

    public array $perPageOptions = [
        15,
        20,
        50,
        100,
    ];

    public ?int $perPage = null;

    public ?bool $isBuilderInstance = null;

    public string $pageName = 'page';

    public array $filters = [];

    public $queryString = [
        'sortField',
        'perPage',
        'filters',
    ];

    public function mount(): void
    {
        $this->filters = $this->filtersWipe();
        $this->queryString = array_merge($this->queryString, [
            $this->pageName => ['except' => 1],
        ]);
    }

    public function render()
    {
        return View::make('tall-datatables::simple-table', [
            'headerData' => $this->headerData(),
            'dataObjects' => $this->dataObjects(),
        ]);
    }

    abstract public function headerData(): Collection;

    abstract public function dataRaw(): array|Collection|QueryBuilder|EloquentBuilder;

    public function dataMapping()
    {
        return static fn ($data) => $data;
    }

    protected function setBuilderInstance(): void
    {
        $returnTypeName = null;
        try {
            $returnTypeName = (new ReflectionMethod(get_class($this), 'dataRaw'))->getReturnType()->getName();
        } catch (\ReflectionException $exception) {
//            return $exception->getMessage();
        }

        $this->isBuilderInstance = false;
        if ($returnTypeName === 'Illuminate\Database\Eloquent\Builder' || $returnTypeName === 'Illuminate\Database\Query\Builder') {
            $this->isBuilderInstance = true;
        }
    }

    public function sortByDataKey(?string $dataKey): void
    {
        if ($dataKey === $this->sortField) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $dataKey;
    }

    protected function filtersWipe(): array
    {
        return $this->wipeFiltersRecursive($this->filters);
    }

    public function dataObjects(): EloquentCollection|Collection|LengthAwarePaginator
    {
        // set the dataType based on the return type of dataRaw
        $this->setBuilderInstance();

        if ($this->isBuilderInstance) {
            return $this->processBuilder();
        }

        return $this->processCollection();
    }

    protected function processBuilder(): LengthAwarePaginator|EloquentCollection
    {
        // EloquentBuilder|QueryBuilder
        $builder = $this->dataRaw();

        foreach ($this->filtersWipe() as $key => $value) {
            if (is_array($value)) {
                $whereBetweenCondition = $this->intervalCheckedValue($value);

                $selectableColumn = Collection::make($builder->getQuery()->columns)
                    ->filter(fn ($val) => is_string($val))
                    ->contains($key);

                if ($selectableColumn) {
                    $builder = $builder->whereBetween($key, [$whereBetweenCondition]);

                    if (in_array(head($value), [0, 0.0, null, ''], true) ||
                        in_array(last($value), [0, 0.0, null, ''], true)
                    ) {
                        $builder = $builder->orWhereNull($key);
                    }
                } else {
                    $builder = $builder->havingBetween($key, [$whereBetweenCondition]);

                    if (in_array(head($value), [0, 0.0, null, ''], true) ||
                        in_array(last($value), [0, 0.0, null, ''], true)
                    ) {
                        $builder = $builder->orHavingRaw("$key IS NULL");
                    }
                }
            } else {
                $builder = $builder->where($key, 'LIKE', $value . '%');
            }
        }

        if (null !== $this->sortField) {
            $builder = $builder->orderBy($this->sortField, $this->sortAsc ? 'ASC' : 'DESC');
        }

        if (null !== $this->perPage) {
            $builder = $builder->paginate($this->perPage, ['*'], $this->pageName);
        } else {
            $builder = $builder->get();
        }

        if (is_a($builder, LengthAwarePaginator::class)) {
            return $builder->through($this->dataMapping());
        }

        return $builder->map($this->dataMapping());
    }

    protected function processCollection(): LengthAwarePaginator|Collection
    {
        $collection = is_array($this->dataRaw()) ? Collection::make($this->dataRaw()) : $this->dataRaw();

        foreach ($this->filtersWipe() as $key => $value) {
            if (is_array($value)) {
                $whereBetweenCondition = $this->intervalCheckedValue($value);

                $collection = $collection->whereBetween($key, $whereBetweenCondition);

                if (in_array(head($value), [0, 0.0, null, ''], true) ||
                    in_array(last($value), [0, 0.0, null, ''], true)
                ) {
                    $builder = $builder->orWhereNull($key);
                }
            } else {
                $collection = $collection->filter(function ($element) use ($key, $value) {
                    $elementValue = (string) data_get($element, $key, '');

                    return Str::of($elementValue)->contains((string) $value);
                });
            }
        }

        if (null !== $this->perPage) {
            $collection = $collection->paginate($this->perPage, $this->pageName, $this->resolvePage());
        }

        if (null !== $this->sortField) {
            $collection = $collection->sortBy($this->sortField, SORT_REGULAR, ! $this->sortAsc);
        }

        if (is_a($collection, LengthAwarePaginator::class)) {
            return $collection->through($this->dataMapping());
        }

        return $collection->map($this->dataMapping());
    }

    protected function wipeFiltersRecursive(array $filters): array
    {
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $filters[$key] = $this->wipeFiltersRecursive($value);
            } elseif (in_array($value, [null, ''], true)) {
                unset($filters[$key]);
            } elseif (is_string($value) && is_numeric($value)) {
                $filters[$key] = ctype_digit($value) ? (int)$value : (float)$value;
            }
        }

        return $filters;
    }

    protected function intervalCheckedValue(array $value): array
    {
        $startOfInterval = $value[0] ?? null;
        $endOfInterval = $value[1] ?? null;

        if (in_array($endOfInterval, [null, ''], true)) {
            $endOfInterval = is_numeric($startOfInterval) ? PHP_INT_MAX : $startOfInterval;
        }

        if (in_array($startOfInterval, [null, ''], true)) {
            $startOfInterval = is_numeric($endOfInterval) ? PHP_INT_MIN : $endOfInterval;
        }

        return [$startOfInterval, $endOfInterval];
    }
}
