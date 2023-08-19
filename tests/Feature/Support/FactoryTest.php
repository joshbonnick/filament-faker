<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

test('configure is applied before data is generated', function(){
    TextInput::configureUsing(function(TextInput $component){
       $component->name('::test::')->afterStateHydrated(function(){
           return '::value::';
       });
    });

    $input = PostResource::faker()->getForm()->schema([
        TextInput::make('safe_email')
    ]);

    expect($input->fake())->toEqual(['::test::' => '::value::']);
});
