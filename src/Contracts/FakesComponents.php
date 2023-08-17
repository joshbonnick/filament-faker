<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Illuminate\Database\Eloquent\Factories\Factory;

interface FakesComponents
{
    public function fake(): mixed;

    /**
     * @param  class-string<Factory>|null  $factory
     */
    public function withFactory(string $factory = null): static;
}
