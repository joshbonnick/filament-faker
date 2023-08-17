<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

interface FakesBlocks
{
    /**
     * @return array<string, mixed>
     */
    public function fake(): array;

    public function shouldFakeUsingComponentName(bool $should = true): static;

    public function mutateFake(Closure $callback): static;

    /**
     * @param  class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(string $factory = null): static;
}
