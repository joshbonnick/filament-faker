<?php

use Filament\Forms\Components\TextInput;
use FilamentBlockFaker\Contracts\BlockFaker;
use FilamentBlockFaker\Tests\TestSupport\Blocks\Block;
use FilamentBlockFaker\Tests\TestSupport\Blocks\BlockWithoutFaker;

it('does not execute slow methods listed in config file', function () {
    $reflection = new ReflectionClass($block = resolve(BlockFaker::class));
    $method = tap($reflection->getMethod('fakeUsingComponentName'))->setAccessible(true);

    config()->set('filament-block-faker.slow_faker_methods', ['test']);

    expect(fn () => $method->invoke($block, TextInput::make('test')))
        ->toThrow(InvalidArgumentException::class, 'test is a disabled method in config.');

    expect($method->invoke($block, TextInput::make('safe_email')))
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can use a faker method if it exists', function () {
    expect($fake = Block::fake())
        ->toBeArray()
        ->toHaveKeys(['data'])
        ->and($fake['data'])
        ->toHaveKey('safe_email')
        ->and($fake['data']['safe_email'])
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can disable the usage of faking by component name', function () {
    expect($fake = Block::fake())
        ->toBeArray()
        ->toHaveKeys(['data'])
        ->and($fake['data'])
        ->toHaveKey('safe_email')
        ->and($fake['data']['safe_email'])
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    config()->set('filament-block-faker.use_component_names_for_fake', false);

    expect($fake = Block::fake())
        ->toBeArray()
        ->toHaveKeys(['data'])
        ->and($fake['data'])
        ->toHaveKey('safe_email')
        ->and($fake['data']['safe_email'])
        ->toBeString()
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can disable the usage of faking by component name with method', function () {
    $fake = Block::fake();

    expect($fake)
        ->toBeArray()
        ->toHaveKeys(['data'])
        ->and($fake['data'])
        ->toHaveKey('safe_email')
        ->and($fake['data']['safe_email'])
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    expect($fake = BlockWithoutFaker::fake())
        ->toBeArray()
        ->toHaveKeys(['data'])
        ->and($fake['data'])
        ->toHaveKey('safe_email')
        ->and($fake['data']['safe_email'])
        ->toBeString()
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

test('mutate fake method is a priority over faker method', function () {
    expect($fake = Block::fake())
        ->toBeArray()
        ->toHaveKeys(['data'])
        ->and($fake['data'])
        ->toHaveKey('phone_number')
        ->and($fake['data']['phone_number'])
        ->toBeString()
        ->toEqual('::phone::');
});

it('uses option value even when faker method is available', function () {
    expect($fake = Block::fake())
        ->toBeArray()
        ->toHaveKeys(['data'])
        ->and($fake['data'])
        ->toHaveKey('company')
        ->and($fake['data']['company'])
        ->toBeIn(['foo', 'bar']);
});
