<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Support\ComponentDecorator;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

beforeEach(function () {
    $this->componentDecorator = tap(resolve(ComponentDecorator::class))->setUp(TextInput::make('test'));
});

it('returns an instance of component', function () {
    expect($this->componentDecorator->getField())->toBeInstanceOf(Field::class);
});

it('can format components', function () {
    $this->componentDecorator->afterStateHydrated(function (?string $state, InjectableService $service) {
        return '::test-two::';
    });

    expect($this->componentDecorator->format())->toEqual('::test-two::');

    $this->componentDecorator->afterStateUpdated(function (?string $state, string $old = null) {
        return '::test::';
    });

    expect($this->componentDecorator->format())->toEqual('::test::');
});

it('only catches ReflectionExceptions thrown by this package.', function () {
    expect(fn () => TextInput::make('test')->afterStateHydrated(fn () => throw new ReflectionException())->fake())
        ->toThrow(ReflectionException::class)
        ->and(fn () => TextInput::make('test')->afterStateUpdated(fn () => throw new ReflectionException())->fake())
        ->toThrow(ReflectionException::class);
});

test('__get function returns a property', function () {
    class ComponentWithPublicProperty extends TextInput
    {
        public string $foobar = 'foo';
    }

    $decorator = tap(resolve(ComponentDecorator::class))->setUp(ComponentWithPublicProperty::make('test'));

    expect($decorator->foobar)->toBeString()->toEqual('foo');
});
