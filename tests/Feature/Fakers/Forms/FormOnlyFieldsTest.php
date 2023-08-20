<?php

use Filament\Forms\Components\TextInput;

it('returns only fields in only fields array', function () {
    $form = mockForm()->schema([
        TextInput::make('foo'),
        TextInput::make('bar'),
        TextInput::make('baz'),
    ]);

    $fake = $form->faker()->onlyFields('foo', 'bar')->fake();
    expect(array_keys($fake))
        ->toEqual(['foo', 'bar'])
        ->and($fake['foo'])
        ->toBeString()
        ->and($fake['bar'])
        ->toBeString();
});
