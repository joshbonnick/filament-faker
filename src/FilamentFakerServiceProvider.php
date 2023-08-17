<?php

declare(strict_types=1);

namespace FilamentFaker;

use FilamentFaker\Contracts\FakerProvider;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use FilamentFaker\Contracts\FakesResources;
use FilamentFaker\Fakers\BlockFaker;
use FilamentFaker\Fakers\ComponentFaker;
use FilamentFaker\Fakers\DefaultFakers;
use FilamentFaker\Fakers\FormFaker;
use FilamentFaker\Fakers\ResourceFaker;
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
            $this->app->singleton(FakerProvider::class, DefaultFakers::class);

            $this->app->bind(FakesBlocks::class, BlockFaker::class);
            $this->app->bind(FakesComponents::class, ComponentFaker::class);
            $this->app->bind(FakesForms::class, FormFaker::class);
            $this->app->bind(FakesResources::class, ResourceFaker::class);
        });
    }
}
