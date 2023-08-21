<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Fakers;

/**
 * @mixin FilamentFaker
 */
interface FakesComponents
{
    /**
     * Generates mock data for a Filament component.
     */
    public function fake(): mixed;
}
