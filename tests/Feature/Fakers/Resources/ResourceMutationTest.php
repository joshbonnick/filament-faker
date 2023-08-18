<?php

use Filament\Forms\Components\Field;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

test('mutateFake can be chained', function () {
    $data = PostResource::faker()->mutateFake(fn (Field $component) => '::test::')->fake();

    expect($data['safe_email'])->toEqual('::test::');
});
