<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Support;

use FilamentFaker\Support\ComponentDecorator;

interface DataGenerator
{
    public function using(ComponentDecorator $component): static;

    public function withOptions(): mixed;

    public function defaultCallback(): string;

    /**
     * @return array<int, string|int|float>
     */
    public function withSuggestions(): array;

    public function date(): string;

    public function file(): string;

    /**
     * @return string[]
     */
    public function keyValue(): array;

    public function color(): string;

    public function html(): string;

    public function checkbox(): bool;
}
