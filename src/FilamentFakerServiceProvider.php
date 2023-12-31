<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Contracts\Fakers\FakesResources;
use FilamentFaker\Contracts\Support\DataGenerator;
use FilamentFaker\Contracts\Support\FilamentFakerFactory;
use FilamentFaker\Contracts\Support\RealTimeFactory;
use FilamentFaker\Contracts\Support\Reflectable;
use FilamentFaker\Decorators\Component;
use FilamentFaker\Fakers\BlockFaker;
use FilamentFaker\Fakers\ComponentFaker;
use FilamentFaker\Fakers\FormFaker;
use FilamentFaker\Fakers\ResourceFaker;
use FilamentFaker\Support\Faker;
use FilamentFaker\Support\FakerFactory;
use FilamentFaker\Support\Reflection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentFakerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-faker')->hasConfigFile();

        $this
            ->registerServices()
            ->macroComponents()
            ->macroBlocks()
            ->macroResources()
            ->macroForms();
    }

    protected function registerServices(): static
    {
        return tap($this, function () {
            $this->app->bind(DataGenerator::class, ComponentDataGenerator::class);
            $this->app->bind(RealTimeFactory::class, Faker::class);
            $this->app->bind(Reflectable::class, Reflection::class);
            $this->app->bind(ComponentDecorator::class, Component::class);
            $this->app->bind(FilamentFakerFactory::class, FakerFactory::class);
            $this->app->bind(FakesBlocks::class, BlockFaker::class);
            $this->app->bind(FakesComponents::class, ComponentFaker::class);
            $this->app->bind(FakesForms::class, FormFaker::class);
            $this->app->bind(FakesResources::class, ResourceFaker::class);
        });
    }

    protected function macroComponents(): static
    {
        return tap($this, function () {
            Field::macro('faker', function (): FakesComponents {
                /* @var Field $this */
                return app(FilamentFakerFactory::class)->component($this);
            });

            Field::macro('fake', fn (): mixed => $this->faker()->fake());
        });
    }

    protected function macroForms(): static
    {
        return tap($this, function () {
            Form::macro('faker', function (): FakesForms {
                /* @var Form $this */
                return app(FilamentFakerFactory::class)->form($this);
            });

            Form::macro('fake', function (): array {
                return $this->faker()->fake();
            });
        });
    }

    protected function macroResources(): static
    {
        return tap($this, function () {
            Resource::macro('faker', function (): FakesResources {
                return app(FilamentFakerFactory::class)->resource(static::class);
            });

            Resource::macro('fake', function (string $form = 'form'): array {
                return static::faker()->withForm($form)->fake();
            });
        });
    }

    protected function macroBlocks(): static
    {
        return tap($this, function () {
            Block::macro('faker', function (string $name = null): FakesBlocks {
                return app(FilamentFakerFactory::class)->block(static::make($name ?? static::class));
            });

            Block::macro('fake', fn (): array => static::faker()->fake());
        });
    }
}
