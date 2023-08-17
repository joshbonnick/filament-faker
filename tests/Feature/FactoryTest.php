<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\PostFactory;

it('can use factory definitions', function () {
    spy(PostFactory::class)->shouldReceive('definition')->andReturn(['title' => '::title::']);
    TextInput::make('title')->faker()->useFactory();
});
