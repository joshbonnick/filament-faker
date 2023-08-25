<?php

namespace FilamentFaker\Tests\Feature\Fakers\Components\Fixtures;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Tests\Feature\Fixtures\InjectableService;

class MutatedComponent extends TextInput
{
    public function mutateFake(Field $component, InjectableService $service): string
    {
        return '::phone::';
    }
}
