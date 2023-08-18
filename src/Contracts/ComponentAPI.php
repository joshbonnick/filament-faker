<?php

namespace FilamentFaker\Contracts;

use Closure;
use Filament\Forms\Components\Field;

/**
 * @internal
 */
interface ComponentAPI
{
    public function component(): Field;

    public function setUp(Field $component): Field;

    public function canBeDateFormatted(): bool;

    public function setState(mixed $state): static;

    public function getAfterStateHydrated(): ?Closure;

    public function hasOptions(): bool;

    public function hasOverride(): bool;
}
