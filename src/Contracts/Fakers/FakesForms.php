<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Fakers;

/**
 * @mixin FilamentFaker
 */
interface FakesForms
{
    /**
     * Generates mock data array for an entire Filament form.
     *
     * @return array<string, mixed>
     */
    public function fake(): array;

    /**
     * Disable or enable the use of hidden fields in the generated data.
     */
    public function withoutHidden(bool $withoutHidden = false): static;

    /**
     * Specify which fields to generate data for.
     *
     * @param  string[]  ...$fields
     */
    public function onlyFields(string ...$fields): static;
}
