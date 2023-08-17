<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Field;

interface FakerProvider
{
    public function withOptions(Field $component): mixed;

    public function defaultCallback(Field $component): string;
}
