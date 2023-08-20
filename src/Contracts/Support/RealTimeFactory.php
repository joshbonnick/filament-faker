<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Support;

use Filament\Forms\Components\Field;

interface RealTimeFactory
{
    public function generate(Field $component): mixed;
}
