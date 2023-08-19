<?php

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\DataGenerator;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\MockPluginComponent;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

it('can use fallback faker method', function () {
    $faker = ($component = MockPluginComponent::make('icon_picker'))->faker();
    $getCallbackMethod = tap((new ReflectionClass($faker))->getMethod('generateComponentData'))->setAccessible(true);

    expect($getCallbackMethod->invoke($faker, $component))->toBeString();
});

test('default entries do not return null', function () {
    $mockBlock = MockBlock::make('test');

    $faker = TextInput::make('test')->faker();

    $method = tap(new ReflectionMethod($faker, 'generateComponentData'))->setAccessible(true);

    foreach ($mockBlock->getChildComponents() as $component) {
        $callback = $method->invoke($faker, $component);

        if ($callback instanceof Closure) {
            expect($callback($component))->not->toBeNull();
        } else {
            expect($callback)->not->toBeNull();
        }
    }
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
        TextInput::class => '::test::',
    ]);

    expect(TextInput::make('test')->fake())->toEqual('::test::');
});

it('supports closures in config overrides', function () {
    expect(TextInput::make('test')->fake())
        ->not->toEqual('::test::')
        ->and(RichEditor::make('test')->fake())
        ->not->toEqual('::test-two::');

    config()->set('filament-faker.fakes', [
        TextInput::class => '::test::',
        RichEditor::class => fn (RichEditor $component) => '::test-two::',
    ]);

    expect(TextInput::make('test')->fake())
        ->toEqual('::test::')
        ->and(RichEditor::make('test')->fake())
        ->toEqual('::test-two::');
});

it('closures added to config are dependency injected', function () {
    config()->set('filament-faker.fakes', [
        TextInput::class => function (InjectableService $service) {
            return '::test::';
        },
    ]);

    expect(TextInput::make('test')->fake())
        ->toEqual('::test::');
});
