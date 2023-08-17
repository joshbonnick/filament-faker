<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\PostFactory;
use FilamentFaker\Tests\TestSupport\Database\factories\TestFactory;
use FilamentFaker\Tests\TestSupport\Models\Post;

it('can use factory definitions', function () {
    $component = TextInput::make('title')->model(Post::class);

    expect($component->faker()->withFactory(TestFactory::class)->fake())
        ->toBeString()->toEqual('::title::')
        ->and($component->faker()->fake())
        ->toBeString()->not->toEqual('::title::');
});
