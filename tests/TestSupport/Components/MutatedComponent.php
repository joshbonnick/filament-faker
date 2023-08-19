<?php

namespace FilamentFaker\Tests\TestSupport\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\TestSupport\Services\InjectableService;

class MutatedComponent extends TextInput
{
    public function mutateFake(Field $component, InjectableService $service): string
    {
        return '::phone::';
    }
}
