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
            Block::macro('fake', fn (): array => app()->make(FakesBlocks::class)->fake(static::make())); // @phpstan-ignore-line
            Field::macro('fake', fn (): mixed => app()->make(FakesComponents::class)->fake($this));
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
