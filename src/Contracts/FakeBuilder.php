<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

interface FakeBuilder
{
    public function fake(): array;
}
