<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Components\MutatedComponent;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

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

test('mutation callbacks are dependency injected', function () {
    $component = TextInput::make('phone_number')->faker()->mutateFake(function (InjectableService $service, Field $field) {
        return $field->getName();
    });

    expect($component->fake())
        ->toBeString()
        ->toEqual('phone_number');
});
