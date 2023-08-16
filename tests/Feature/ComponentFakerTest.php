<?php

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use FilamentFaker\ComponentFaker;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Components\MockPluginComponent;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

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
        if (! in_array($tag, $suggestions)) {
            fail("[$tag] was not in the suggestions array.");
        }
    }
});

it('can use fallback faker method', function () {
    $faker = tap(resolve(ComponentFaker::class))->fake($component = MockPluginComponent::make('icon_picker'));
    $getCallbackMethod = tap((new ReflectionClass($faker))->getMethod('getCallback'))->setAccessible(true);

    expect($getCallbackMethod->invoke($faker, $component))->toBeCallable();
});

test('default entries do not return null', function () {
    $mockBlock = MockBlock::make('test');

    $faker = tap(resolve(ComponentFaker::class))->fake(TextInput::make('test'));

    $method = tap(new ReflectionMethod($faker, 'getCallback'))->setAccessible(true);

    foreach ($mockBlock->getChildComponents() as $component) {
        $callback = $method->invoke($faker, $component);
        expect($callback($component))->not->toBeNull();
    }
});

it('uses methods added to config first', function () {
    expect(TextInput::make('test')->fake())->not->toEqual('::test::');

    config()->set('filament-faker.fakes', [
        TextInput::class => fn () => '::test::',
    ]);

    expect(TextInput::make('test')->fake())->toEqual('::test::');
});

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

it('respects formatStateUsing', function () {
    $component = TextInput::make('email')->formatStateUsing(fn (string $state) => str($state)->wrap('<b>')->toString());

    expect(resolve(FakesComponents::class)->fake($component))
        ->toBeString()
        ->toContain('<b>');
});
