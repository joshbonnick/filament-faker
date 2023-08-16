<?php

namespace FilamentBlockFaker\Tests\TestSupport\Blocks;

use Filament\Forms\Components\Field;

class BlockWithoutFaker extends Block
{
    public function shouldFakeUsingComponentName(Field $component): bool
    {
        return false;
    }
}
