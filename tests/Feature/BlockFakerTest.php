<?php

use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;

it('can generate fake blocks content', function () {
    expect($fake = MockBlock::fake())
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

it('can mutate a specific component', function () {
    expect(MockBlock::make())
        ->toHaveMethod('mutateFake')
        ->and($fake = MockBlock::fake())
        ->toBeArray()
        ->toHaveKeys(['type', 'data'])
        ->and($fake['data'])
        ->not
        ->toBeEmpty()
        ->and($fake['data']['phone_number'])
        ->toBe('::phone::');
});
