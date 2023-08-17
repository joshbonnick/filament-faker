<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\TestFactory;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('can use factory definitions', function () {
    $component = TextInput::make('title')->model(Post::class);
    $factory = resolve(TestFactory::class);

    expect($component->faker()->withFactory(TestFactory::class)->fake())
        ->toBeString()
        ->toEqual($title = $factory->definition()['title'])
        ->and($component->faker()->fake())
        ->toBeString()
        ->not
        ->toEqual($title);
});

it('can use factory through block faker', function () {
    $form = PostResource::faker()->getForm();
    $factory = resolve(TestFactory::class);

    expect($fake = $form->faker()->withFactory(TestFactory::class)->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toEqual($factory->definition()['title'])
        ->and($fake = $form->faker()->fake())
        ->toHaveKey('title')
        ->and($fake['title'])
        ->not
        ->toEqual($factory->definition()['title']);
});

it('can use factory through resource faker', function () {
    $factory = resolve(TestFactory::class);

    expect($fake = PostResource::faker()->withFactory(TestFactory::class)->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toEqual($factory->definition()['title'])
        ->and($fake = PostResource::faker()->fake())
        ->toHaveKey('title')
        ->and($fake['title'])
        ->not
        ->toEqual($factory->definition()['title'])
        ->and($fake = PostResource::fake())
        ->toHaveKey('title')
        ->and($fake['title'])
        ->not
        ->toEqual($factory->definition()['title']);
});

it('will return only keys added to onlyAttributes', function () {
    $form = PostResource::faker()->getForm()->schema([
        TextInput::make('title'),
        TextInput::make('content'),
    ]);

    $factory = resolve(TestFactory::class);

    expect($fake = $form->faker()->withFactory(TestFactory::class, ['title'])->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toEqual($factory->definition()['title'])
        ->and($fake['content'])
        ->not
        ->toEqual($factory->definition()['content']);

});
