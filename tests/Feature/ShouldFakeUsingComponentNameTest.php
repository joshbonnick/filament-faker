<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('can disable the usage of faking by component name by chaining on blocks', function () {
    $data = MockBlock::faker()->shouldFakeUsingComponentName(false)->fake();

    expect($data['data']['safe_email'])
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    $data = MockBlock::faker()->shouldFakeUsingComponentName(true)->fake();

    expect($data['data']['safe_email'])
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can disable the usage of faking by component name by chaining on resources', function () {
    $data = PostResource::faker()->shouldFakeUsingComponentName(false)->fake();

    expect($data['safe_email'])
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    $data = PostResource::faker()->shouldFakeUsingComponentName(true)->fake();

    expect($data['safe_email'])
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can disable the usage of faking by component name by chaining on forms', function () {
    $form = PostResource::faker()->getForm()->schema([
        TextInput::make('safe_email'),
    ]);

    $data = $form->faker()->shouldFakeUsingComponentName(false)->fake();

    expect($data['safe_email'])
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    $data = $form->faker()->shouldFakeUsingComponentName(true)->fake();

    expect($data['safe_email'])
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

it('can disable the usage of faking by component name by chaining on components', function () {
    $data = TextInput::make('safe_email')->faker()->shouldFakeUsingComponentName(false)->fake();

    expect($data)
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    $data = TextInput::make('safe_email')->faker()->shouldFakeUsingComponentName(true)->fake();

    expect($data)
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});
