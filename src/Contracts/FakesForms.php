<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

interface FakesForms
{
    /**
     * @return array<string, mixed>
     */
    public function fake(): array;

    public function withoutHidden(bool $withoutHidden = false): static;
}
