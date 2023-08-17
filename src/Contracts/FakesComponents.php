<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Field;

interface FakesComponents
{
    /**
     * Generates mock data for a Filament component.
     */
    public function fake(): mixed;

    public function setUpComponent(Field $component): Field;
}
