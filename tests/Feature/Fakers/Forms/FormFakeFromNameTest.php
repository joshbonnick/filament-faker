<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('can disable the usage of faking by component name by chaining', function () {
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
