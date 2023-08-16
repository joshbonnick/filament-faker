<?php

declare(strict_types=1);

namespace JoshBonnick\FilamentBlockFaker;

use Filament\Forms\Components\Builder\Block;
use JoshBonnick\FilamentBlockFaker\Contracts\BlockFaker as IBlockFaker;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentBlockFakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-block-faker')
            ->hasConfigFile();

        $this->app->bind(IBlockFaker::class, BlockFaker::class);

        Block::macro('fake', function (string $name = 'faked') {
            /** @var class-string<Block> $block */
            $block = static::class;

            return app()->make(IBlockFaker::class)->fake($block, $name);
        });
    }
}
