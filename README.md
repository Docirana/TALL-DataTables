# TALL Datatables
Tailwind, Alpine.js, Laravel, and Livewire simple datatables

# Installation

You can install the TALL Datatables package via composer.

```sh
composer require docirana/tall-datatables
```

## Tailwind Configuration

::: info
You can skip this step if you are planning to customize the views.
:::

The TALL Datatables has views so you need to adjust your Tailwind CSS config.

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        // ...

        "./vendor/docirana/tall-datatables/src/views/**/*.blade.php"
    ]
}
```

## Publishing Views

To make the tables blend into the style of your project, you may wish to make changes to the layout. This can be done by
publishing the views and customize them any way you like.

```sh
php artisan vendor:publish --provider="Docirana\TallDatatables\Providers\TallDatatablesServiceProvider"
```
# Example

```sh
php artisan livewire:make ExampleComponent
```

```php
<?php

namespace App\Http\Livewire;

use Docirana\TallDatatables\TallDatatables;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class ExampleComponent extends TallDatatables
{
    public function headerData(): Collection
    {
        // TODO: Implement headerData() method.
    }

    public function dataRaw(): array|Collection|QueryBuilder|EloquentBuilder
    {
        // TODO: Implement dataRaw() method.
    }
}

```
!!! by the dataRaw you need to specify only one return type !!!
This is depending on your data.
