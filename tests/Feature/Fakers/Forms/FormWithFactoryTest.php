<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\TestFactory;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Models\WithoutFactory;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

beforeEach(function () {
    mockComponentDecorator();
});

it('can use factory definitions', function () {
    $form = PostResource::faker()->getForm();
    $factory = resolve(TestFactory::class);

    expect($fake = $form->faker()->withFactory(TestFactory::class)->fake())
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

test('factory can be retrieved if binding resolution is thrown', function () {
    $faker = PostResource::faker()
        ->getForm()
        ->model(Post::class)
        ->faker()
        ->withFactory('beepboop');

    expect($fake = $faker->fake())
        ->toBeArray()
        ->toHaveKey('title')
        ->and($fake['title'])
        ->toBeString();
});

test('exception is thrown if cannot resolve model', function () {
    expect(fn () => PostResource::faker()->getForm()->faker()->withFactory()->fake())
        ->toThrow(InvalidArgumentException::class, 'Unable to find Model for form.');
});

test('exception is thrown if model does not use HasFactory', function () {
    expect(fn () => PostResource::faker()->getForm()->model(WithoutFactory::class)->faker()->withFactory()->fake())
        ->toThrow(
            InvalidArgumentException::class,
            'Unable to find Factory for '.WithoutFactory::class
        );
});
