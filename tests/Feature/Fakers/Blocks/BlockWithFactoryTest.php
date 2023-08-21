<?php

use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Database\factories\TestFactory;

it('can use factory definitions', function () {
    $block = MockBlock::faker()->withFactory(TestFactory::class);
    $factory = resolve(TestFactory::class);

    expect($fake = $block->fake())
        ->toBeArray()
        ->and($fake['data']['title'])
        ->toEqual($factory->definition()['title']);
});

it('will return only keys added to onlyAttributes', function () {
    $block = MockBlock::faker();

    $factory = resolve(TestFactory::class);

    [$title, $content] = array_values($factory->definition());

    expect($fake = $block->withFactory(TestFactory::class, ['title'])->fake())
        ->toBeArray()
        ->and($fake['data']['title'])
        ->toEqual($title)
        ->and($fake['data']['content'])
        ->not
        ->toEqual($content);
});

it('throws an exception if factory is not provided', function () {
    expect(fn () => MockBlock::faker()->withFactory()->fake())
        ->toThrow(InvalidArgumentException::class, 'You must provide a Factory.');
});

test('exception is thrown if cannot resolve model', function () {
    expect(fn () => MockBlock::faker('faked')->withFactory('foo')->fake())
        ->toThrow(InvalidArgumentException::class, 'Unable to find Model for [faked] block.');
});
