<?php

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use FilamentFaker\Contracts\FakesResources;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'log'])
    ->each->not->toBeUsed();

test('config file entries for fakes are all callable', function () {
    expect(config('filament-faker.fakes'))->toBeEmpty();
});

it('uses strict types')
    ->expect('FilamentFaker')
    ->toUseStrictTypes();

test('only interfaces are in contracts directory')
    ->expect('FilamentFaker\Contracts')
    ->toBeInterfaces();

test('macros return correct implementations', function () {
    expect(PostResource::faker())
        ->toBeInstanceOf(FakesResources::class)
        ->and(PostResource::faker()->getForm()->faker())
        ->toBeInstanceOf(FakesForms::class)
        ->and(TextInput::make('test')->faker())
        ->toBeInstanceOf(FakesComponents::class)
        ->and(Block::make('test')->faker())
        ->toBeInstanceOf(FakesBlocks::class);
});
