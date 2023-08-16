<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use FilamentFaker\Contracts\FakesBlocks;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentFakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-faker')
            ->hasConfigFile();

        $this->app->bind(FakesBlocks::class, BlockFaker::class);

        Block::macro('fake', function (string $name = 'faked') {
            /** @var class-string<Block> $block */
            $block = static::class;

            return app()->make(FakesBlocks::class)->fake($block, $name);
        });
    }
}
