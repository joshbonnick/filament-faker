<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\ComponentWithoutFakingFromNames;
use FilamentFaker\Tests\TestSupport\Components\MutatedComponent;

it('does not execute slow methods listed in config file', function () {
    config()->set('filament-faker.slow_faker_methods', ['safeEmail']);

    expect(TextInput::make('safe_email')->fake())
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
        ->and(TextInput::make('email')->fake())
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

});

it('can use a faker method if it exists', function () {
    expect(TextInput::make('safe_email')->fake())
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can disable the usage of faking by component name', function () {
    expect(TextInput::make('safe_email')->fake())
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    config()->set('filament-faker.use_component_names_for_fake', false);

    expect(TextInput::make('safe_email')->fake())
        ->toBeString()
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can disable the usage of faking by component name with method', function () {
    expect(TextInput::make('safe_email')->fake())
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
        ->and($component = ComponentWithoutFakingFromNames::make('safe_email'))
        ->toHaveMethod('shouldFakeUsingComponentName')
        ->and($component->fake())
        ->toBeString()
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

});

test('mutate fake method is a priority over faker method', function () {
    expect($component = MutatedComponent::make('phone_number'))
        ->toHaveMethod('mutateFake')
        ->and($component->fake())
        ->toBeString()
        ->toEqual('::phone::');
});

it('uses option value even when faker method is available', function () {
    expect(MockBlock::fake()['data']['company'])->toBeIn(['foo', 'bar']);
});
