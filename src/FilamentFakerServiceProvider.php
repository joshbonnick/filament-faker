<?php

declare(strict_types=1);

namespace FilamentFaker;

use FilamentFaker\Contracts\DataGenerator;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use FilamentFaker\Contracts\FakesResources;
use FilamentFaker\Contracts\RealTimeFactory;
use FilamentFaker\Fakers\BlockFaker;
use FilamentFaker\Fakers\ComponentFaker;
use FilamentFaker\Fakers\FormFaker;
use FilamentFaker\Fakers\ResourceFaker;
use FilamentFaker\Support\Faker;
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
            $this->app->singleton(DataGenerator::class, ComponentDataGenerator::class);
            $this->app->bind(RealTimeFactory::class, Faker::class);
            $this->app->bind(FakesBlocks::class, BlockFaker::class);
            $this->app->bind(FakesComponents::class, ComponentFaker::class);
            $this->app->bind(FakesForms::class, FormFaker::class);
            $this->app->bind(FakesResources::class, ResourceFaker::class);
        });
    }
}
