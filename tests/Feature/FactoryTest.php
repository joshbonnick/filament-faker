<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\TestFactory;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Models\WithoutFactory;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('can use factory definitions', function () {
    $component = TextInput::make('title')->model(Post::class);
    $factory = resolve(TestFactory::class);

    expect($component->faker()->withFactory(TestFactory::class)->fake())
        ->toBeString()
        ->toEqual($factory->definition()['title']);
});

it('can use factory through block faker', function () {
    $form = PostResource::faker()->getForm();
    $factory = resolve(TestFactory::class);

    expect($fake = $form->faker()->withFactory(TestFactory::class)->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toEqual($factory->definition()['title']);
});

it('can use factory through resource faker', function () {
    $factory = resolve(TestFactory::class);

    expect($fake = PostResource::faker()->withFactory(TestFactory::class)->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toEqual($factory->definition()['title']);
});

it('will return only keys added to onlyAttributes', function () {
    $form = PostResource::faker()->getForm()->schema([
        TextInput::make('title'),
        TextInput::make('content'),
    ]);

    $factory = resolve(TestFactory::class);

    [$title, $content] = array_values($factory->definition());

    expect($fake = $form->faker()->withFactory(TestFactory::class, ['title'])->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toEqual($title)
        ->and($fake['content'])
        ->not
        ->toEqual($content);
});

test('factory can be retrieved from resource if binding resolution is thrown', function () {
    expect($fake = PostResource::faker()->withFactory('beepboop')->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toBeString();
});

test('exception is thrown if cannot resolve model', function () {
    expect(fn () => TextInput::make('test')->faker()->withFactory('')->fake())
        ->toThrow(InvalidArgumentException::class, 'Unable to find Model for test');
});

test('exception is thrown if model does not use HasFactory', function () {
    expect(fn () => TextInput::make('test')->model(WithoutFactory::class)->faker()->withFactory('')->fake())
        ->toThrow(
            InvalidArgumentException::class,
            'Unable to find Factory for '.WithoutFactory::class
        );
});
