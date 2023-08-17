<?php

declare(strict_types=1);

namespace FilamentFaker;

use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use FilamentFaker\Fakers\BlockFaker;
use FilamentFaker\Fakers\ComponentFaker;
use FilamentFaker\Fakers\FormFaker;
use FilamentFaker\Support\Macros;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentFakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-faker')->hasConfigFile();

        $this->registerServices();

        $this->app->make(Macros::class)->register();
    }

    protected function registerServices(): static
    {
        return tap($this, function () {
            $this->app->bind(FakesBlocks::class, BlockFaker::class);
            $this->app->bind(FakesComponents::class, ComponentFaker::class);
            $this->app->bind(FakesForms::class, FormFaker::class);
        });
    }
}
