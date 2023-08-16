<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'log'])
    ->each->not->toBeUsed();

test('config file entries for fakes are all callable', function () {
    expect(config('filament-faker.fakes'))
        ->not
        ->toBeEmpty()
        ->toContainOnlyInstancesOf(Closure::class);
});

test('config file entries for fakes do not return null', function () {
    foreach (config('filament-faker.fakes') as $component => $callback) {
        if (! $callback instanceof Closure) {
            fail("$component does not return a Closure");
        }

        $result = $component === 'default'
            ? $callback(TextInput::make('test'))
            : $callback(tap(new $component('test'), function (Field $field) {
                if (method_exists($field, 'options')) {
                    $field->options(['foo' => 'bar']);
                }
            }));

        expect($result)->not->toBeNull("$component returned null");
    }
});

it('uses strict types')
    ->expect('FilamentFaker')
    ->toUseStrictTypes();

test('only interfaces are in contracts directory')
    ->expect('FilamentFaker\Contracts')
    ->toBeInterfaces();
