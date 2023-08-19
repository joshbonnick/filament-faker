<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

interface RealTimeFactory
{
    public function fakeFromName(string $name): mixed;
}
