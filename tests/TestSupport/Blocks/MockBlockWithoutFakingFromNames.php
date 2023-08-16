<?php

namespace FilamentFaker\Tests\TestSupport\Blocks;

use Filament\Forms\Components\Field;

class MockBlockWithoutFakingFromNames extends MockBlock
{
    public function shouldFakeUsingComponentName(Field $component): bool
    {
        return false;
    }
}
