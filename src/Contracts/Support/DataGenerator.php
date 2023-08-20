<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Support;

use FilamentFaker\Decorators\ComponentDecorator;

interface DataGenerator
{
    public function uses(ComponentDecorator $component): static;

    public function generate(): mixed;
}
