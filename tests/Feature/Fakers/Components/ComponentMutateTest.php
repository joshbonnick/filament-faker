<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Components\MutatedComponent;

test('mutateFake can be chained onto components', function () {
    expect(TextInput::make('test')->faker()->mutateFake(fn (TextInput $input) => '::test::')->fake())
        ->toEqual('::test::');
});

test('mutate fake method is a priority over faker method', function () {
    expect($component = MutatedComponent::make('phone_number'))
        ->toHaveMethod('mutateFake')
        ->and($component->fake())
        ->toBeString()
        ->toEqual('::phone::');
});

test('mutateFake macros are used', function () {
    TextInput::macro('mutateFake', fn () => '::test::');

    expect(TextInput::make('test')->fake())
        ->toEqual('::test::');

    TextInput::flushMacros();
});

test('mutateFake closure macros are used', function () {
    TextInput::macro('mutateFake', function () {
        return function (Field $component) {
            if ($component->getName() === 'test') {
                return '::test::';
            }

            return null;
        };
    });

    expect(TextInput::make('test')->fake())
        ->toEqual('::test::');

    TextInput::flushMacros();
});
