<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Builder\Block;

interface FakesBlocks
{
    /**
     * @return array{type: string, data: array<string, mixed>}
     */
    public function fake(Block $block): array;
}
