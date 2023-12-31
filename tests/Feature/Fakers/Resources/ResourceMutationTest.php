<?php

use Filament\Forms\Components\Field;
use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\MutatedResource;
use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\PostResource;

beforeEach(function () {
    mockComponentDecorator();
});

test('mutateFake can be chained', function () {
    $data = PostResource::faker()
        ->mutateFake(
            fn (Field $component) => $component->getName() === 'safe_email' ? '::test::' : null)
        ->fake();

    expect($data['safe_email'])->toEqual('::test::');
});

test('resources can have mutateFake method', function () {
    $data = MutatedResource::faker()->fake();
    expect($data['safe_email'])->toEqual('::mutated-in-resource::');
});
