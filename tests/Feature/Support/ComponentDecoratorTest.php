<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Support\ComponentDecorator;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

beforeEach(function () {
    $this->component = tap(resolve(ComponentDecorator::class))->setUp(TextInput::make('test'));
});

it('returns an instance of component', function () {
    expect($this->component->component())->toBeInstanceOf(Field::class);
});

it('can format components', function () {
    $this->component->component()->afterStateHydrated(function (?string $state, InjectableService $service) {
        return '::test-two::';
    });

    expect($this->component->format())->toEqual('::test-two::');

    $this->component->component()->afterStateUpdated(function (?string $state, string $old = null) {
        return '::test::';
    });

    expect($this->component->format())->toEqual('::test::');
});

it('only catches ReflectionExceptions thrown by this package.', function () {
    expect(fn () => TextInput::make('test')->afterStateHydrated(fn () => throw new ReflectionException())->fake())
        ->toThrow(ReflectionException::class)
        ->and(fn () => TextInput::make('test')->afterStateUpdated(fn () => throw new ReflectionException())->fake())
        ->toThrow(ReflectionException::class);
});
