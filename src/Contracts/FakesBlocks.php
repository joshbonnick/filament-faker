<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

interface FakesBlocks
{
    /**
     * Generates mock data array for a Filament block.
     *
     * @return array<string, mixed>
     */
    public function fake(): array;
}
