<?php

declare(strict_types=1);

namespace Docirana\TallDatatables\Providers;

use Illuminate\Support\ServiceProvider;

class TallDatatablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'tall-datatables');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/tall-datatables'),
        ]);
    }
}
