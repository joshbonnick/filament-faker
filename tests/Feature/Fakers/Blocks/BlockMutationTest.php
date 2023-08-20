<?php

use Filament\Forms\Components\Field;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;

test('mutateFake can be chained onto blocks', function () {
    $data = MockBlock::faker()->mutateFake(function (Field $component) {
        if ($component->getName() === 'safe_email') {
            return '::test::';
        }

        return null;
    })->fake();

    expect($data['data']['safe_email'])->toEqual('::test::');
});
