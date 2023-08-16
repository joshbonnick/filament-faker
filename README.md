# Filament Faker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/joshbonnick/filament-faker.svg?style=flat-square)](https://packagist.org/packages/joshbonnick/filament-block-faker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/joshbonnick/filament-faker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/joshbonnick/filament-block-faker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/joshbonnick/filament-faker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/joshbonnick/filament-faker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/joshbonnick/filament-faker.svg?style=flat-square)](https://packagist.org/packages/joshbonnick/filament-block-faker)

Generate fake content for Filament forms, blocks and components.

## Requirements

- [Filament](https://github.com/filamentphp/filament) v3 or higher.
- PHP 8.1 or higher.

## Installation

You can install the package via composer:

```bash
composer require joshbonnick/filament-faker
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-faker-config"
```

## Usage

Call the `fakeForm` method on a resource to retrieve and array of fields filled with fake data.

```php
<?php

$data = PostResource::fakeForm();
```

By default, component names are used to map to a Faker method for more accurate data. There are several ways to disable
this behavior:

Set `use_component_names_for_fake` to `false` in `config/filament-faker.php` which will disable the behavior for
the entire package as default.

Add a `shouldFakeUsingComponentName` method to your `Block` or `Component`, the method should return a `bool`

```php
<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;

class HeadingBlock extends Block
{
    public function shouldFakeUsingComponentName(): bool
    {
        return false;
    }
}
```

## Usage In Tests

You can use the faked data in your tests.

```php
<?php

namespace Tests\Feature\Services\ContentFormatting;

use App\Contracts\ContentFormatter;
use App\Filament\Blocks\HeadingBlock;
use App\Filament\Resources\PostResource;
use Tests\TestCase;

class FormatBlocksTest extends TestCase
{
    public function test_it_formats_blocks()
    {
        $blocks = [
            HeadingBlock::fake(),
        ];
        
        // $blocks = [
        //    [
        //        'type' => 'App\Filament\Blocks\HeadingBlock'
        //        'data' => [
        //             'level'   => 1,
        //             'content' => 'Maecenas id ipsum interdum, porta diam in, molestie est.',
        //        ],
        //    ],
        // ];

        $service = app()->make(ContentFormatter::class);
        $content $service->format($blocks);
        // or...
        $data = PostResource::fakeForm();
        $content $service->format($data);
        
        // Make assertions of your formatted content...
    }
}
```

## Faking Custom Blocks

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

If you do not register extra components, the `default` item in the config file will be used which returns the result of
`fake()->sentence()`.

You may also override the default faker method attached to built in components by adding them to the config.

## Mutating Faker Callback

If you wish to fake a specific components value, you can add a `mutateFake` method which accepts an instance of
the component and returns the faked value.

When faking a block the `mutateFake` method is used as a priority over `Component` class fakes.

```php
<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;

class MutatedComponent extends TextInput
{
    public function mutateFake(Field $component): string
    {
        return fake()->randomHtml();
    }
}
```
```php
<?php

namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder\Block;

class HeadingBlock extends Block
{
    public function mutateFake(Field $component): string
    {
        return match($component->getName()){
            'level' => fake()->numberBetween(1, 5),
        };
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Josh](https://github.com/joshbonnick)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
