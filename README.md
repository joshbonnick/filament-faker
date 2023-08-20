# Filament Faker

## Filament Testing Utility Library

[![Latest Version on Packagist](https://img.shields.io/packagist/v/joshbonnick/filament-faker.svg?style=flat-square)](https://packagist.org/packages/joshbonnick/filament-faker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/joshbonnick/filament-faker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/joshbonnick/filament-faker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/joshbonnick/filament-faker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/joshbonnick/filament-faker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/joshbonnick/filament-faker.svg?style=flat-square)](https://packagist.org/packages/joshbonnick/filament-faker)

Filament Faker is a utility library designed to streamline testing for Filament resources, forms, blocks, and 
components. This library assists in automatically generating mock data for your tests within the Filament ecosystem.

## Features and Usage Highlights

* **Data Generation:** Automatically generate test data for Filament resources, forms, blocks, and components.
* **Factory Support:** Utilize factory definitions for precise and accurate data generation.
* **Mutations:** Modify specific component values to suit your testing scenarios.
* **Configurable:** Control the behavior of data generation using configuration options.
* **Seamless Integration:** Easily integrate the library into your Filament-based projects.

## Contents

<!-- TOC -->
* [Filament Utility Library](#filament-utility-library)
* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
  * [Usage In Tests](#usage-in-tests)
  * [Faking Custom & Plugin Components](#faking-custom--plugin-components)
  * [Mutating Generated Data](#mutating-generated-data)
  * [Generate Data Using Factory Definitions](#generate-data-using-factory-definitions)
    * [Selecting Definitions](#selecting-definitions)
* [IDE Support](#ide-support)
* [Changelog](#changelog)
* [Credits](#credits)
* [License](#license)
<!-- TOC -->

## Requirements

- [Filament](https://github.com/filamentphp/filament) v3 or higher.
- PHP 8.1 or higher.

## Installation

You can install the package via composer:

```bash
composer require joshbonnick/filament-faker --dev
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-faker-config"
```

## Usage

Call the `fake` method on a resource to retrieve an array of fields filled with fake data.

```php
<?php

$data = PostResource::fake();
```

<details>
  <summary>Generated Data Example</summary>

```php
[
  "title" => "Hello World",
  "slug" => "hello-world",
  "meta-description" => "Ut voluptas molestiae sint repudiandae sint et quis.",
  "content" => [
    [
      "type" => "App\Filament\Blocks\Heading",
      "data" => [
        "content" => "Impedit ex odio nostrum.",
        "level" => "h5",
      ],
    ],
    [
      "type" => "App\Filament\Blocks\RichEditor",
      "data" => [
        "content" => "<p>Non est molestiae et quia reiciendis et iste.</p>",
      ],
    ],
    [
      "type" => "App\Filament\Blocks\Image",
      "data" => [
        "file" => "https://placehold.co/600x400.png",
        "alt" => "Et nam aut nobis alias possimus voluptatem.",
      ],
    ],
  ],
  "status" => "draft",
  "categories" => [2],
]
```
</details>

### Usage In Tests

You can use the faked data in your tests.

```php
<?php

namespace Tests\Feature\Services\ContentFormatting;

use App\Contracts\ContentFormatter;
use App\Filament\Blocks\HeadingBlock;
use App\Filament\Resources\PostResource;
use Filament\Forms\Components\Field;use Tests\TestCase;

class FormatBlocksTest extends TestCase
{
    public function test_it_formats_blocks()
    {
        $blocks = [
            HeadingBlock::fake(),
        ];

        $service = app()->make(ContentFormatter::class);
        $content = $service->format($blocks);
        // or...
        $data = PostResource::fake();
        $content = $service->format($data);
        // or apply mutations...
        $data = PostResource::faker()->mutateFake(fn (Field $component): ?string => match ($component->getName()) {
            'title' => fake()->jobTitle(),
            default => null,
        })->fake();
        
        // Test the content...
    }
}
```

### Faking Custom & Plugin Components

If you have added a plugin such as [Spatie Media Library](https://filamentphp.com/plugins/filament-spatie-media-library),
which adds the `SpatieMediaLibraryFileUpload` component you can register it in `config/filament-faker.php` like so:

```php
<?php

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

return [
    'fakes' => [
        SpatieMediaLibraryFileUpload::class => fn (SpatieMediaLibraryFileUpload $component) => fake()->imageUrl(),
    ],
];
```

If you do not register extra components, the `default` callback will be used which returns the result of
`fake()->sentence()`.

You may also override the default faker method attached to built in components by adding them to the config.

### Mutating Generated Data

If you need to control a specific components value, you can chain `mutateFake` onto the fake builder. If this method returns
`null` for a component then it will be ignored and filled by other methods.

```php
<?php

use Filament\Forms\Components\Field;
use Illuminate\Support\Str;
use App\Services\InjectableService;

$data = PostResource::faker()->mutateFake(function (Field $component, InjectableService $service): mixed {
    return match ($component->getName()) {
        'title' => fake()->jobTitle(),
        default => null,
    };
});
```

#### Mutate Method As Method

Alternatively you can add a `mutateFake` method to your Form, Block or Resource.

The closure passed to `mutateFake` supports dependency injection, you just need to type hint `\Filament\Forms\Components\Field`
or the specific component type (e.g. `\Filament\Forms\Components\TextInput`) to get an instance of the component.

```php
<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use App\Services\InjectableService;

class MutatedComponent extends TextInput
{
    public function mutateFake(Field $component, InjectableService $service): string
    {
        return $service->getSomething();
    }
}
```

### Disabling Generations From Component Names

By default, component names are used to map to a Faker method for more accurate data. There are several ways to disable
this behavior:

Set `use_component_names_for_fake` to `false` in `config/filament-faker.php` which will disable the behavior for
the entire package as default.

You can chain `shouldFakeUsingComponentName` on the Faker API to disable the feature per test.

```php
<?php

$data = PostResource::faker()->shouldFakeUsingComponentName(false)->fake();
// or
$data = PostResource::faker()->form()->shouldFakeUsingComponentName(false)->fake();
// or
$data = MyCustomBlock::faker()->shouldFakeUsingComponentName(false)->fake();
```

### Generate Data Using Factory Definitions

If you need increased accuracy for a specific test then you can enable the usage of Factories. When the use of factories 
is enabled the generated data will be generated using definitions from the factory provided. 

If no factory is provided the package will attempt to resolve one from the given resource, form, component or block.

As this feature executes `Factory::makeOne` under the hood, I recommend only using it in tests where the accuracy of the faked 
data is of significant importance.

```php
<?php

namespace Tests\Feature\Services\ContentFormatting;

use App\Contracts\ContentFormatter;
use App\Filament\Resources\PostResource;
use Tests\TestCase;

class FormatBlocksTest extends TestCase
{
    public function test_it_formats_blocks()
    {
        $data = PostResource::faker()->withFactory()->fake();

        $service = app()->make(ContentFormatter::class);
        $content = $service->format($data);
        
        // Make assertions of your formatted content...
    }
}
```

If you need to specify a factory you can pass a `class-string` or instance of a `Factory` to the `withFactory()` method.

Only `Resources` can resolve a factory automatically, if you wish to use a factory with a Block or Component, you must provide
either the factory to `withFactory` or provide the model to the `Component`, `Form` or `Block`.

#### Selecting Definitions

If you want to select only a specific set of definitions from your factory you can pass an `array` as to the `withFactory()` method 
which lists the definitions you want you use.

```php
$data = PostResource::faker()->withFactory(onlyAttributes: ['title', 'slug'])->fake();
```

### Generate Data for Specific Fields

If you only need a specific field or fields, you can specify them with the `onlyFields` method on Resource and Form fakers.

```php
$data = PostResource::faker()->onlyFields('title', 'slug', 'published_at')->fake();
```

## IDE Support

As this package adds methods using Laravel's `Macroable` trait, your IDE will not find the methods on its own. To fix this you will need
to use the [ide-helper package](https://github.com/barryvdh/laravel-ide-helper).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Josh](https://github.com/joshbonnick)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
