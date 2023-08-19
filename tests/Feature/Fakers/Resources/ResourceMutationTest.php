<?php

use Filament\Forms\Components\Field;
use FilamentFaker\Tests\TestSupport\Resources\MutatedResource;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

test('mutateFake can be chained', function () {
    $data = PostResource::faker()->mutateFake(fn (Field $component) => '::test::')->fake();

    expect($data['safe_email'])->toEqual('::test::');
});

test('resources can have mutateFake method', function () {
    $data = MutatedResource::faker()->fake();
    expect($data['safe_email'])->toEqual('::mutated-in-resource::');
});
