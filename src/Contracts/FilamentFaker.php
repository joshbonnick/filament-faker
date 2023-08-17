<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

interface FilamentFaker
{
    /**
     * @param  array<int, string>  $onlyAttributes
     * @param  Factory<Model>|class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(Factory|string $factory = null, array $onlyAttributes = []): static;

    public function shouldFakeUsingComponentName(bool $should = true): static;

    public function mutateFake(Closure $callback = null): static;
}
