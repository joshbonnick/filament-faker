<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\PostResource;

test('mutateFake can be chained onto forms', function () {
    $form = PostResource::faker()->getForm()->schema([
        TextInput::make('safe_email'),
    ]);
    $data = $form->faker()->mutateFake(fn (Field $component) => '::test::')->fake();
    expect($data['safe_email'])->toEqual('::test::');
});
