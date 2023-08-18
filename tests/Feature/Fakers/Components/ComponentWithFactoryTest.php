<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Database\factories\TestFactory;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Models\WithoutFactory;

it('can use factory definitions', function () {
    $component = TextInput::make('title')->model(Post::class);
    $factory = resolve(TestFactory::class);

    expect($component->faker()->withFactory(TestFactory::class)->fake())
        ->toBeString()
        ->toEqual($factory->definition()['title']);
});

it('will return only keys added to onlyAttributes', function () {
    $input = TextInput::make('title')
        ->model(Post::class)
        ->faker()
        ->withFactory(TestFactory::class, ['title']);

    expect($input->fake())
        ->toEqual(resolve(TestFactory::class)->definition()['title']);
});

test('factory can be retrieved if binding resolution is thrown', function () {
    expect(TextInput::make('title')
        ->model(Post::class)
        ->faker()
        ->withFactory('foobar')
        ->fake()
    )->toBeString();
});

test('exception is thrown if cannot resolve model', function () {
    expect(fn () => TextInput::make('test')->faker()->withFactory('')->fake())
        ->toThrow(InvalidArgumentException::class, 'Unable to find Model.');
});

test('exception is thrown if model does not use HasFactory', function () {
    expect(fn () => TextInput::make('test')->model(WithoutFactory::class)->faker()->withFactory('')->fake())
        ->toThrow(
            InvalidArgumentException::class,
            'Unable to find Factory for '.WithoutFactory::class
        );
});