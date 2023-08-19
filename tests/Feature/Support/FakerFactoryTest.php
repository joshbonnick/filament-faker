<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\Support\RealTimeFactory;

it('does not execute excluded methods listed in config file', function () {
    $factory = resolve(RealTimeFactory::class);
    config()->set('filament-faker.excluded_faker_methods', ['safeEmail']);

    expect(TextInput::make('safe_email')->fake())
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
        ->and(TextInput::make('email')->fake())
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
        ->and($factory->fakeFromName('safeEmail'))
        ->toBeNull();
});
