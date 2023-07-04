<?php

declare(strict_types=1);

namespace Docirana\TallDatatables\Columns;

class NumericColumn extends BaseColumn
{
    public string $viewFile = 'numeric';

    public float $inputStep = 1.0;

    public ?int $maximum = null;

    public ?int $minimum = null;

    public function __construct()
    {
        //
    }

    public function setInputStepTo(float $inputStep): self
    {
        $this->inputStep = $inputStep;

        return $this;
    }

    public function setMaximum(?int $maximum): self
    {
        $this->maximum = $maximum;

        return $this;
    }

    public function setMinimum(?int $minimum): self
    {
        $this->minimum = $minimum;

        return $this;
    }
}
