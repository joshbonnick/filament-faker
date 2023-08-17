<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\PostFactory;

it('can use factory definitions', function () {
    spy(PostFactory::class)->shouldReceive('definition')->andReturn(['title' => '::title::']);

    expect(TextInput::make('title')->faker()->withFactory()->fake())
        ->toBeString()->toEqual('::title::')
        ->and(TextInput::make('title')->faker()->fake())
        ->toBeString()->not->toEqual('::title::');
});
