<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

interface FakesComponents
{
    public function fake(): mixed;

    /**
     * @param  class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(string $factory = null): static;

    public function shouldFakeUsingComponentName(bool $should = true): static;
}
