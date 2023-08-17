<?php

use Filament\Forms\Components\TextInput;

it('respects formatStateUsing', function () {
    $component = TextInput::make('email')->formatStateUsing(fn (string $state) => str($state)->wrap('<b>')->toString());

    expect($component->faker()->fake())
        ->toBeString()
        ->toContain('<b>');
});
