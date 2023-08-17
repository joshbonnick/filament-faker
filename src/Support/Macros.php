<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;

class Macros
{
    public function register(): void
    {
        $this
            ->macroComponents()
            ->macroBlocks()
            ->macroResources()
            ->macroForms();
    }

    protected function macroComponents(): static
    {
        return tap($this, function () {
            Field::macro('faker', function () {
                /* @var Field $this */
                return app()->make(FakesComponents::class, ['component' => $this]);
            });

            Field::macro('fake', fn (): mixed => $this->faker()->fake()); // @phpstan-ignore-line
        });
    }

    protected function macroForms(): static
    {
        return tap($this, function () {
            Form::macro('faker', function (): FakesForms {
                /* @var Form $this */
                return app()->make(FakesForms::class, ['form' => $this]);
            });

            Form::macro('fake', function (): array {
                return $this->faker()->fake(); // @phpstan-ignore-line
            });
        });
    }

    protected function macroResources(): static
    {
        return tap($this, function () {
            Resource::macro('fakeForm', function (string $form = 'form') {
                $formBase = Form::make(FormsMock::make());

                return rescue(
                    callback: fn () => static::$form($formBase)->fake(),
                    rescue: fn () => resolve(static::class)->{$form}($formBase)->fake()
                );
            });
        });
    }

    protected function macroBlocks(): static
    {
        return tap($this, function () {
            Block::macro('faker', function (string $name = 'faked'): FakesBlocks {
                return app()->make(FakesBlocks::class, ['block' => static::make($name)]); // @phpstan-ignore-line
            });

            Block::macro('fake', fn (): array => static::faker()->fake()); // @phpstan-ignore-line
        });
    }
}
