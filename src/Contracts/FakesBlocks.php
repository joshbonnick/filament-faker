<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Field;

interface FakesBlocks
{
    /**
     * @return array<string, mixed>
     */
    public function fake(): array;

    public function shouldFakeUsingComponentName(bool $should = true): static;

    public function mutateFake(Field $component): static;
}
