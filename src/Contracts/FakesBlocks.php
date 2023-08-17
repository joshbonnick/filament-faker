<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

interface FakesBlocks
{
    /**
     * @return array<string, mixed>
     */
    public function fake(): array;

    public function shouldFakeUsingComponentName(bool $should = true): static;
}
