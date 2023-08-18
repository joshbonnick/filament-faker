<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Field;

interface DataGenerator
{
    public function withOptions(Field $component): mixed;

    public function defaultCallback(Field $component): string;

    /**
     * @return array<int, string|int|float>
     */
    public function withSuggestions(Field $component): array;

    public function date(): string;

    public function file(Field $upload): string;

    /**
     * @return string[]
     */
    public function keyValue(Field $component): array;

    public function color(Field $color): string;

    public function html(): string;

    public function checkbox(): bool;
}
