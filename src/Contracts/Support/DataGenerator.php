<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Support;

use FilamentFaker\Support\ComponentDecorator;

interface DataGenerator
{
    public function uses(ComponentDecorator $component): static;

    public function generate(): mixed;
}
