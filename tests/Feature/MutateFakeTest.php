<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

test('mutateFake can be chained onto resources', function () {
    $data = PostResource::faker()->mutateFake(fn (Field $component) => '::test::')->fake();

    expect($data['safe_email'])->toEqual('::test::');
});

test('mutateFake can be chained onto forms', function () {
    $form = PostResource::faker()->getForm()->schema([
        TextInput::make('safe_email'),
    ]);
    $data = $form->faker()->mutateFake(fn (Field $component) => '::test::')->fake();
    expect($data['safe_email'])->toEqual('::test::');
});

test('mutateFake can be chained onto blocks', function () {
    $data = MockBlock::faker()->mutateFake(fn (Field $component) => '::test::')->fake();

    expect($data['data']['safe_email'])->toEqual('::test::');
});

test('mutateFake can be chained onto components', function () {
    expect(TextInput::make('test')->faker()->mutateFake(fn (TextInput $input) => '::test::')->fake())
        ->toEqual('::test::');
});

test('mutateFake macros are used', function () {
    TextInput::macro('mutateFake', fn () => '::test::');

    expect(TextInput::make('test')->fake())
        ->toEqual('::test::');
});
