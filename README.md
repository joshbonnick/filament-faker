# Filament Block Faker

[![Latest Version on Packagist](https://img.shields.io/packagist/v/joshbonnick/filament-block-faker.svg?style=flat-square)](https://packagist.org/packages/joshbonnick/filament-block-faker)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/joshbonnick/filament-block-faker/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/joshbonnick/filament-block-faker/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/joshbonnick/filament-block-faker/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/joshbonnick/filament-block-faker/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/joshbonnick/filament-block-faker.svg?style=flat-square)](https://packagist.org/packages/joshbonnick/filament-block-faker)

Generate fake blocks content for Filament's Block feature for use in testing.

## Requirements

- [Filament](https://github.com/filamentphp/filament) v3 or higher
- PHP 8.1

## Installation

You can install the package via composer:

```bash
composer require joshbonnick/filament-block-faker
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-block-faker-config"
```

## Usage

Create a block class, so you may easily reference it in your tests.

```php
namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder;

class MyContentBlock extends Builder\Block
{    
    public static function make(string $name): static
    {
        return parent::make($name)
            ->label('Rich Editor')
            ->icon('heroicon-m-bars-3-bottom-left')
            ->schema([
                Components\Select::make('color')
                     ->options([
                         '#f00' => 'Red',
                         '#0f0' => 'Green',
                         '#00f' => 'Blue'
                     ])  
                     ->required(),
                Components\RichEditor::make('content')->required(),
            ]);
    }
}
```

### Using The Block

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Blocks\MyContentBlock;
use Filament\Forms;
use Filament\Forms\Form;

class PostResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Builder::make('content_blocks')->blocks([
                MyContentBlock::make('my_content'),
            ]),
        ]);        
    }
}
```

Now we have access to the `fake` method for our block which will return a Faker method that matches the component name or
a fallback faker method defined in `config/filament-block-faker.php`.

The `fake` method returns the content of the block as if it had been filled in by a user, using `faker` methods.

### Disable Component Name Usage

If you do not wish to use the components name for a faker method and just the generic faker methods assigned to a component
you can set `use_component_names_for_fake` to `false` in `config/filament-block-faker.php` or override the `shouldFakeUsingComponentName`
and return false.

```php
public function shouldFakeUsingComponentName(): bool
{
    return false;
}
```

### Usage In Tests

```php
namespace Tests\Feature\Services\ContentFormatting;

use App\Contracts\ContentFormatter;
use App\Filament\Blocks\MyContentBlock;
use Tests\TestCase;

class FormatBlocksTest extends TestCase
{
    public function test_it_formats_blocks()
    {
        $blocks = [
            MyContentBlock::fake(),
            MyContentBlock::fake(),
        ];
        
        // $blocks = [
        //    [
        //        'type' => '\\App\\Filament\\Blocks\\MyContentBlock'
        //        'data' => [
        //             'color'   => '#f00',
        //             'content' => 'Maecenas id ipsum interdum, porta diam in, molestie est.',
        //        ],
        //    ],
        //    [
        //        'type' => '\\App\\Filament\\Blocks\\MyContentBlock'
        //        'data' => [
        //             'color'   => '#0f0',
        //             'content' => 'Quisque id ex est. Ut feugiat enim neque, non scelerisque nisi ullamcorper sit amet.',
        //        ],
        //    ],
        // ];

        $service = app()->make(ContentFormatter::class);
        $service->format($blocks);
        
        // Make assertions of your formatted content...
    }
}
```

## Faking Custom Blocks

If you have added a plugin such as [Spatie Media Library](https://filamentphp.com/plugins/filament-spatie-media-library),
which adds the `SpatieMediaLibraryFileUpload` component you can register it in `config/filament-block-faker.php` like so:

```php
<?php

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

return [
    'files' => [
        SpatieMediaLibraryFileUpload::class => fn (SpatieMediaLibraryFileUpload $component) => fake()->imageUrl(),
        
        // ...rest of faker methods
    ]
];
```

If you do not register extra components, the `default` item in the config file will be used which returns the result of
`fake()->sentence()`.

## Faking Specific Components

If you wish to fake a specific components value, you can override the `mutateFake` method which accepts an instance of
the component.

When faking a block the `mutateFake` method is used as a priority over `Component` class fakes.

### Examples

#### Email Field

In this example we are returning a `Closure` which returns `fake()->safeEmail()` for the field named `email`. You must
return `null` if you do not want to provide a fake for that specific component and rely on the fakes defined in 
`config/filament-block-faker.php`.

```php
namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder;

class MyContentBlock extends Builder\Block
{    
    /**
     * @return null|Closure(Components\Field $component): mixed
     */
    public function mutateFake(Components\Field $component): ?Closure
    {
        return match ($component->getName()) {
            'email' => fn () => fake()->safeEmail(),
            default => null,
        };
    }
    
    public static function make(string $name): static
    {
        return parent::make($name)
            ->label('Rich Editor')
            ->icon('heroicon-m-bars-3-bottom-left')
            ->schema([
                Components\Select::make('color')
                     ->options([
                         '#f00' => 'Red',
                         '#0f0' => 'Green',
                         '#00f' => 'Blue'
                     ])  
                     ->required(),
                Components\RichEditor::make('content')->required(),
                Components\TextInput::make('email')->email()->required(),
            ]);
    }
}
```

#### Based On Record

In this example we are first trying to match a fake callback to the field's name, if that does not exist we move onto to
checking the `status` property of the `Model` the form is using.

```php
namespace App\Filament\Blocks;

use Filament\Forms\Components\Builder;

class MyContentBlock extends Builder\Block
{    
    /**
     * @return null|Closure(Components\Field $component): mixed
     */
    public function mutateFake(Components\Field $component): ?Closure
    {
        if ($component->getName() === 'email') {
            return fn () => fake()->safeEmail();
        }

        if ($component->getName() === 'published_at'
        && $component->getRecord()->status === PostStatus::Published) {
            return fn () => now();
        }

        return null;
    }
    
    public static function make(string $name): static
    {
        return parent::make($name)
            ->label('Rich Editor')
            ->icon('heroicon-m-bars-3-bottom-left')
            ->schema([
                Components\Select::make('color')
                     ->options([
                         '#f00' => 'Red',
                         '#0f0' => 'Green',
                         '#00f' => 'Blue'
                     ])  
                     ->required(),
                Components\RichEditor::make('content')->required(),
                Components\TextInput::make('email')->email()->required(),
                Components\DateTimePicker::make('published_at')->date()->required(),
            ]);
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
