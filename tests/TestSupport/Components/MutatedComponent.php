<?php

namespace FilamentFaker\Tests\TestSupport\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;

class MutatedComponent extends TextInput
{
    public function mutateFake(Field $component): string
    {
        return '::phone::';
    }
}
