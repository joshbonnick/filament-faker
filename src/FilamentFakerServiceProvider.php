<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

class FilamentFakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-faker')->hasConfigFile();

        $this->registerServices()->registerMacros();
    }

    protected function registerMacros(): static
    {
        return tap($this, function () {
            Block::macro('fake', function (string $name = 'faked') {
                /** @var class-string<Block> $block */
                $block = static::class;

                return app()->make(FakesBlocks::class)->fake($block, $name);
            });

            Field::macro('fake', function (string $name = 'faked') {
                /** @var class-string<Field> $field */
                $field = static::class;

                try {
                    return app()->make(FakesComponents::class)->fake($this);
                } catch (Throwable $e) {
                }

                return app()->make(FakesComponents::class)->fake($field::make($name));
            });
        });
    }

    protected function registerServices(): static
    {
        return tap($this, function () {
            $this->app->bind(FakesBlocks::class, BlockFaker::class);
            $this->app->bind(FakesComponents::class, ComponentFaker::class);
        });
    }
}
