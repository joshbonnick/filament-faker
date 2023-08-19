<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Support;

interface RealTimeFactory
{
    public function fakeFromName(string $name): mixed;
}
