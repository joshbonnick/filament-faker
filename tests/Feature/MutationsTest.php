<?php


use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\ComponentWithoutFakingFromNames;
use FilamentFaker\Tests\TestSupport\Components\MutatedComponent;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

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

it('does not execute slow methods listed in config file', function () {
    config()->set('filament-faker.slow_faker_methods', ['safeEmail']);

    expect(TextInput::make('safe_email')->fake())
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
        ->and(TextInput::make('email')->fake())
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('uses methods added to config first', function () {
    expect(TextInput::make('test')->fake())->not->toEqual('::test::');

    config()->set('filament-faker.fakes', [
        TextInput::class => fn () => '::test::',
    ]);

    expect(TextInput::make('test')->fake())->toEqual('::test::');
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
