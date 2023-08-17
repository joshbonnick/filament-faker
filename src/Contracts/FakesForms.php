<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

interface FakesForms
{
    /**
     * @return array<string, mixed>
     */
    public function fake(): array;

    public function withoutHidden(bool $withoutHidden = false): static;

    /**
     * @param  class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(string $factory = null): static;
}
