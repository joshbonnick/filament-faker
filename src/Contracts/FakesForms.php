<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

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
}
