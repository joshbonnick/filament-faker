<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\FakerProvider;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\MockPluginComponent;

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

        if($callback instanceof Closure){
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
