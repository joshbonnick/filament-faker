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
        Block::macro('fake', fn (): array => app()->make(FakesBlocks::class)->fake(static::make())); // @phpstan-ignore-line

        Field::macro('fake', fn (): mixed => app()->make(FakesComponents::class)->fake($this)); // @phpstan-ignore-line

        Resource::macro('fakeForm', function (string $form = 'form') {
            $formBase = Form::make(FormsMock::make());

            return rescue(
                callback: fn () => static::$form($formBase)->fake(),
                rescue: fn () => resolve(static::class)->{$form}($formBase)->fake()
            );
        });

        Form::macro('fake',
            fn (bool $withHidden = false): array => app()->make(FakesForms::class)->fake($this, $withHidden) // @phpstan-ignore-line
        );
    }
}
