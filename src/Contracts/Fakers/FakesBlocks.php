<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Fakers;

/**
 * @mixin FilamentFaker
 */
interface FakesBlocks
{
    /**
     * Generates mock data array for a Filament block.
     *
     * @return array<string, mixed>
     */
    public function fake(): array;

    /**
     * Specify which fields to generate data for.
     *
     * @param  string[]  ...$fields
     */
    public function onlyFields(string ...$fields): static;
}
