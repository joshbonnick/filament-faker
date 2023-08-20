<?php

use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;

it('can mutate a specific component', function () {
    expect(MockBlock::make())
        ->toHaveMethod('mutateFake')
        ->and($fake = MockBlock::faker()->fake())
        ->toBeArray()
        ->toHaveKeys(['type', 'data'])
        ->and($fake['data'])
        ->not
        ->toBeEmpty()
        ->and($fake['data']['phone_number'])
        ->toBe('::phone::');
});

it('can disable the usage of faking by component name by chaining on blocks', function () {
    $data = MockBlock::faker()->shouldFakeUsingComponentName(false)->fake();

    expect($data['data']['safe_email'])
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    $data = MockBlock::faker()->shouldFakeUsingComponentName(true)->fake();

    expect($data['data']['safe_email'])
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});
