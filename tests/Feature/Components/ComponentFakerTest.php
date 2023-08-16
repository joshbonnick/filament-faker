<?php

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use FilamentFaker\ComponentFaker;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\MockPluginComponent;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

it('can use fallback faker method', function () {
    $faker = tap(resolve(ComponentFaker::class))->fake($component = MockPluginComponent::make('icon_picker'));
    $getCallbackMethod = tap((new ReflectionClass($faker))->getMethod('getCallback'))->setAccessible(true);

    expect($getCallbackMethod->invoke($faker, $component))->toBeCallable();
});

test('default entries do not return null', function () {
    $mockBlock = MockBlock::make('test');

    $faker = tap(resolve(ComponentFaker::class))->fake(TextInput::make('test'));

    $method = tap(new ReflectionMethod($faker, 'getCallback'))->setAccessible(true);

    foreach ($mockBlock->getChildComponents() as $component) {
        $callback = $method->invoke($faker, $component);
        expect($callback($component))->not->toBeNull();
    }
});

it('uses methods added to config first', function () {
    expect(TextInput::make('test')->fake())->not->toEqual('::test::');

    config()->set('filament-faker.fakes', [
        TextInput::class => fn () => '::test::',
    ]);

    expect(TextInput::make('test')->fake())->toEqual('::test::');
});
