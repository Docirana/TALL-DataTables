<?php

declare(strict_types=1);

namespace Docirana\TallDatatables\Columns;

abstract class BaseColumn
{
    // just the key
    public string $key;

    // table header column
    public string $label;

    // filterable column
    public bool $filterable = false;

    // sortable column
    public bool $sortable = true;

    public string $viewFile;

    public static function make(string $key, ?string $label = null): self
    {
        return (new static())
            ->forKey($key)
            ->withLabel($label ?? ucwords(Str::of($key)->replace(['_', '-'], ' ')));
    }

    public function forKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function withLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function filterable(): self
    {
        $this->filterable = true;

        return $this;
    }

    public function notFilterable(): self
    {
        $this->filterable = false;

        return $this;
    }

    public function sortable(): self
    {
        $this->sortable = true;

        return $this;
    }

    public function notSortable(): self
    {
        $this->sortable = false;

        return $this;
    }
}
