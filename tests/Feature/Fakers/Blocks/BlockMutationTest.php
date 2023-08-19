<?php

use Filament\Forms\Components\Field;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;

test('mutateFake can be chained onto blocks', function () {
    $data = MockBlock::faker()->mutateFake(fn (Field $component) => '::test::')->fake();

    expect($data['data']['safe_email'])->toEqual('::test::');
});
