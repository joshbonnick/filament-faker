<?php

namespace FilamentFaker\Tests\TestSupport\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;

class ComponentWithoutFakingFromNames extends TextInput
{
    public function shouldFakeUsingComponentName(Field $component): bool
    {
        return false;
    }
}
