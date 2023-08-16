<?php

namespace FilamentFaker\Tests\TestSupport\Blocks;

use Filament\Forms\Components\Field;

class BlockWithoutFakingFromNames extends Block
{
    public function shouldFakeUsingComponentName(Field $component): bool
    {
        return false;
    }
}
