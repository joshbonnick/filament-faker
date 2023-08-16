<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Field;

interface FakesComponents
{
    public function fake(Field $component): mixed;
}
