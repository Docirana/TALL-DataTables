<?php

declare(strict_types=1);

namespace JustSteveKing\DataObjects\Tests;

use Docirana\TallDatatables\TallDatatables;
use Orchestra\Testbench\TestCase;

class PackageTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TallDatatables::class,
        ];
    }
}
