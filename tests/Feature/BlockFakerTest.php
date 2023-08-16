<?php

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use FilamentBlockFaker\BlockFaker;
use FilamentBlockFaker\Tests\TestSupport\Blocks\Block;

it('can generate fake blocks content', function () {
    expect($fake = Block::fake())
        ->toBeArray()
        ->toHaveKeys(['type', 'data'])
        ->and($fake['data'])
        ->not
        ->toBeEmpty()
        ->toHaveKeys([
            'color',
            'content',
            'radio',
            'tags',
            'suggested_tags',
            'checkbox',
            'checkbox_list',
            'toggle',
            'datetime',
            'some_image',
            'some_file',
            'key_value',
            'color_rgb',
            'color_rgba',
            'color_hex',
            'color_hsl',
            'color_hex',
        ])
        ->and($fake['data']['color'])
        ->toBeIn(['#f00', '#0f0', '#00f'])
        ->and($fake['data']['radio'])
        ->toBeIn(['#f00', '#0f0', '#00f'])
        ->and($fake['data']['tags'])
        ->toBeArray()
        ->and($fake['data']['suggested_tags'])
        ->toBeArray()
        ->toContain('foo')
        ->and($fake['data']['checkbox'])
        ->toBeBool()
        ->and($fake['data']['checkbox_list'])
        ->toBeArray()
        ->and($fake['data']['toggle'])
        ->toBeBool()
        ->and($fake['data']['datetime'])
        ->toBeInstanceOf(DateTime::class)
        ->and($fake['data']['some_image'])
        ->toBeString()
        ->toContain('.png')
        ->and($fake['data']['some_file'])
        ->toBeString()
        ->toContain('.txt')
        ->and($fake['data']['key_value'])
        ->toBeArray()
        ->toHaveCount(1)
        ->and($fake['data']['color_rgb'])
        ->toBeString()
        ->toStartWith('rgb(')
        ->and($fake['data']['color_rgba'])
        ->toBeString()
        ->toStartWith('rgba(')
        ->and($fake['data']['color_hex'])
        ->toBeString()
        ->toStartWith('#')
        ->and($fake['data']['color_hsl'])
        ->toBeString()
        ->toStartWith('hsl(');
});

it('can use fallback faker method', function () {
    $fakes = config('filament-block-faker.fakes');

    expect($fakes['default'])
        ->toBeCallable()
        ->and($fakes['default'](Radio::make('test')))->toBeString();
});

it('can mutate a specific component', function () {
    $callable = (new Block('test'))->mutateFake($component = TextInput::make('email_field'));

    expect($callable)
        ->toBeCallable()
        ->and($callable($component))
        ->toBeString()
        ->toEqual('dev@example.com');
});

it('can accept name parameter', function () {
    $block = tap(resolve(BlockFaker::class))->fake(Block::class, $name = Str::random(8));
    $blockProperty = tap((new ReflectionClass($block))->getProperty('block'))->setAccessible(true);

    expect($blockProperty->getValue($block)->getName())->toEqual($name);
});
