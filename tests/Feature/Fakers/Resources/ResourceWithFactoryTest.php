<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\TestFactory;
use FilamentFaker\Tests\TestSupport\Models\WithoutFactory;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;
use FilamentFaker\Tests\TestSupport\Resources\WithoutFactoryResource;

beforeEach(function () {
    mockComponentDecorator();
});

it('can use factory definitions', function () {
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

test('exception is thrown if model does not use HasFactory', function () {
    expect(fn () => WithoutFactoryResource::faker()->withFactory()->fake())
        ->toThrow(
            InvalidArgumentException::class,
            'Unable to find Factory for '.WithoutFactory::class
        );
});
