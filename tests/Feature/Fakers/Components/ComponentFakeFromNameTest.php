<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;

it('can disable the usage of faking by component name by chaining', function () {
    $data = TextInput::make('safe_email')->faker()->shouldFakeUsingComponentName(false)->fake();

    expect($data)
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    $data = TextInput::make('safe_email')->faker()->shouldFakeUsingComponentName(true)->fake();

    expect($data)
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can use a faker method if it exists', function () {
    expect(TextInput::make('safe_email')->fake())
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('uses option value even when faker method is available', function () {
    expect(MockBlock::fake()['data']['company'])->toBeIn(['foo', 'bar']);
});

it('can disable the usage of faking by component name from config', function () {
    expect(TextInput::make('safe_email')->fake())
        ->toBeString()
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    config()->set('filament-faker.fake_using_component_name', false);

    expect(TextInput::make('safe_email')->fake())
        ->toBeString()
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});
