<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Support\Reflection;
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

it('afterStateHydrated hook return null if method does not exist', function () {
    $mock = mock(Reflection::class);

    $mock->shouldReceive('reflect')
        ->andReturnSelf();
    $mock->shouldReceive('property')
        ->andThrow(ReflectionException::class, 'afterStateHydrated does not exist');

    app()->instance(Reflection::class, $mock);

    $componentDecorator = tap(app(ComponentDecorator::class))->setUp(TextInput::make('test'));

    expect($componentDecorator->getAfterStateHydrated('test'))
        ->toBeNull();
});

it('afterStateUpdated hook return null if method does not exist', function () {
    $mock = mock(Reflection::class);

    $mock->shouldReceive('reflect')
        ->andReturnSelf();
    $mock->shouldReceive('property')
        ->andThrow(ReflectionException::class, 'afterStateUpdated does not exist');

    app()->instance(Reflection::class, $mock);

    $componentDecorator = tap(app(ComponentDecorator::class))->setUp(TextInput::make('test'));

    expect($componentDecorator->getAfterStateUpdated('test'))
        ->toBeNull();
});

test('__get function returns a property', function () {
    $mock = mock(TextInput::class)->makePartial();
    $mock->shouldReceive('make')->andReturnSelf()->set('foobar', 'foo');

    $decorator = tap(resolve(ComponentDecorator::class))->setUp($mock::make('test'));

    expect($decorator->foobar)->toBeString()->toEqual('foo');
});

test('is_a method with array and string', function () {
    expect($this->componentDecorator->is_a(Field::class))
        ->toBeTrue()
        ->and($this->componentDecorator->is_a(TextInput::class, RichEditor::class))
        ->toBeTrue();
});

it('returns a searchable option', function () {
    $searchCallback = function (InjectableService $service) {
        return $service->search();
    };

    $select = Select::make('some_option')->searchable()->getSearchResultsUsing($searchCallback);
    /** @var ComponentDecorator $decorator */
    $decorator = tap(resolve(ComponentDecorator::class))->setUp($select);

    expect($decorator->getSearch())->toEqual(array_keys(app()->call($searchCallback)));
});
