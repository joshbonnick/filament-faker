<?php

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

it('uses an option value when options are a query', function () {
    $posts = Post::factory()->count(2)->create();

    $select = Select::make('parent_id')
        ->relationship('parent', 'title')
        ->label('Primary Category')
        ->searchable()
        ->options(fn () => Post::query()->select(['id', 'title'])->get()->pluck('title', 'id'))
        ->required();

    expect($select->fake())->toBeIn($posts->pluck('id')->toArray());
});

it('uses an option value when options use dependency injection', function () {
    Post::factory()->count(2)->create();

    $select = Select::make('parent_id')
        ->relationship('parent', 'title')
        ->label('Primary Category')
        ->searchable()
        ->options(fn (InjectableService $service) => $service->get()->pluck('title', 'id')->toArray())
        ->required();

    $options = resolve(InjectableService::class)->get()->pluck('title', 'id')->keys()->toArray();

    expect($select->fake())->toBeIn($options);
});

it('can fake components with options', function () {
    $components = [
        Select::class,
        Radio::class,
    ];

    foreach ($components as $component) {
        $component = $component::make('test')->options([
            'foo' => 'bar',
            'bar' => 'foo',
            'hello' => 'world',
        ]);

        expect($component->fake())
            ->toBeString()
            ->toBeIn(['foo', 'bar', 'hello']);
    }
});

it('returns an entry of the suggestions array for tags', function () {
    $tags = TagsInput::make('tags')->suggestions($suggestions = ['foo', 'bar', 'hello world'])->fake();

    expect($tags)->toBeArray();

    foreach ($tags as $tag) {
        expect($tag)->toBeIn($suggestions, "[$tag] was not in the suggestions array.");
    }
});

it('returns an array if field with options is multiselectable', function () {
    $select = Select::make('test')->options(['foo' => 'bar', 'hello' => 'world']);

    expect($select->fake())
        ->toBeString()
        ->toBeIn(['foo', 'hello'])
        ->and($select->multiple()->fake())
        ->toBeArray();
});
