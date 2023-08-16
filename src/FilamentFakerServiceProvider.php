<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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

            Field::macro('fake', fn (): mixed => app()->make(FakesComponents::class)->fake($this)); // @phpstan-ignore-line

            Resource::macro('fakeForm', function (string $form = 'form') {
                $formBase = Form::make(resolve(HasForms::class));

                return rescue(
                    callback: fn () => static::$form($formBase)->fake(),
                    rescue: fn () => (new static())->{$form}($formBase)->fake()
                );
            });

            Form::macro('fake',
                fn (bool $withHidden = false): array => app()->make(FakesForms::class)->fake($this, $withHidden) // @phpstan-ignore-line
            );
        });
    }

    protected function registerServices(): static
    {
        return tap($this, function () {
            $this->app->bind(FakesBlocks::class, BlockFaker::class);
            $this->app->bind(FakesComponents::class, ComponentFaker::class);
            $this->app->bind(FakesForms::class, FormFaker::class);

            $this->app->bind(HasForms::class, EditRecord::class);
        });
    }
}
