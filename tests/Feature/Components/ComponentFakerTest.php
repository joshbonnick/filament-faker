<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\FakerProvider;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\ComponentWithoutFakingFromNames;
use FilamentFaker\Tests\TestSupport\Components\MockPluginComponent;
use FilamentFaker\Tests\TestSupport\Components\MutatedComponent;

it('can use fallback faker method', function () {
    $faker = ($component = MockPluginComponent::make('icon_picker'))->faker();
    $getCallbackMethod = tap((new ReflectionClass($faker))->getMethod('getFake'))->setAccessible(true);

    expect($getCallbackMethod->invoke($faker, $component))->toBeCallable();
});

test('default entries do not return null', function () {
    $mockBlock = MockBlock::make('test');

    $faker = TextInput::make('test')->faker();

    $method = tap(new ReflectionMethod($faker, 'getFake'))->setAccessible(true);

    foreach ($mockBlock->getChildComponents() as $component) {
        $callback = $method->invoke($faker, $component);

        if ($callback instanceof Closure) {
            expect($callback($component))->not->toBeNull();
        } else {
            expect($callback)->not->toBeNull();
        }
    }
});

it('uses methods added to config first', function () {
    expect(TextInput::make('test')->fake())->not->toEqual('::test::');

    config()->set('filament-faker.fakes', [
        TextInput::class => fn () => '::test::',
    ]);

    expect(TextInput::make('test')->fake())->toEqual('::test::');
});

test('value is still returned when exception is thrown', function () {
    class TestField extends Field
    {
        protected string $name = 'test';
    }
    $component = mock(TestField::class)->makePartial();
    $component->shouldReceive('state')->andThrow(ReflectionException::class);

    expect(resolve(FakesComponents::class, compact('component'))
        ->fake())->not->toBeNull();
});

it('handles invalid options field', function () {
    expect(resolve(FakerProvider::class)
        ->withOptions(TextInput::make('test')))
        ->toBeString();
});

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
