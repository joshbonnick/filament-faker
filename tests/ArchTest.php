<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'log'])
    ->each->not->toBeUsed();

test('config file entries for fakes are all callable', function () {
    expect(config('filament-faker.fakes'))->toBeEmpty();
});

it('uses strict types')
    ->expect('FilamentFaker')
    ->classes()
    ->toUseStrictTypes();

test('only interfaces are in contracts directory')
    ->expect('FilamentFaker\Contracts')
    ->toBeInterfaces();

test('only traits are in concerns directory')
    ->expect('FilamentFaker\Concerns')
    ->toBeTraits();
