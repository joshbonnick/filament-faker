<?php

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\DataGenerator;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\MockPluginComponent;

it('can use fallback faker method', function () {
    $faker = ($component = MockPluginComponent::make('icon_picker'))->faker();
    $getCallbackMethod = tap((new ReflectionClass($faker))->getMethod('getFake'))->setAccessible(true);

    expect($getCallbackMethod->invoke($faker, $component))->toBeString();
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

test('value is still returned when reflection exception is thrown', function () {
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
    expect(resolve(DataGenerator::class)
        ->withOptions(TextInput::make('test')))
        ->toBeString();
});

it('returns a date from date components', function () {
    $datepicker = DatePicker::make('published_at')
        ->label('Published Date')
        ->nullable()
        ->date();

    expect($carbon = Carbon::parse($datepicker->fake()))
        ->not
        ->toThrow(InvalidFormatException::class)
        ->and($carbon->isValid())
        ->toBeTrue();
});

test('faker returns an instance of FakesComponents', function () {
    expect(TextInput::make('test')->faker())
        ->toBeInstanceOf(FakesComponents::class);
});

it('uses methods added to config first', function () {
    expect(TextInput::make('test')->fake())->not->toEqual('::test::');

    config()->set('filament-faker.fakes', [
        TextInput::class => fn () => '::test::',
    ]);

    expect(TextInput::make('test')->fake())->toEqual('::test::');
});
