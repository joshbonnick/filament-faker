<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\FakesComponents;

it('respects formatStateUsing', function () {
    $component = TextInput::make('email')->formatStateUsing(fn (string $state) => str($state)->wrap('<b>')->toString());

    expect(resolve(FakesComponents::class)->fake($component))
        ->toBeString()
        ->toContain('<b>');
});
