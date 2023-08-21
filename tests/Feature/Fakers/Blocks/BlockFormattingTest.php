<?php

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use FilamentFaker\Contracts\Support\RealTimeFactory;
use Illuminate\Support\Str;

it('can format other fields', function () {
    $mock = mock(RealTimeFactory::class);
    $mock->shouldReceive('generate')->andReturn('Hello World');

    app()->instance(RealTimeFactory::class, $mock);

    $form = mockForm()->schema([
        Builder::make('test')->blocks([
            Builder\Block::make('some_block')
                ->schema([
                    TextInput::make('title')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set) {
                            return $set('slug', Str::slug($state));
                        }),
                    TextInput::make('slug')
                        ->live(onBlur: true)
                        ->dehydrated(),
                ]),
        ]),
    ]);

    $data = $form->fake();

    expect($data['test'])
        ->and($data['test'][0]['data']['slug'])
        ->toEqual('hello-world');
});
