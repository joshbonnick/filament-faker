<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Builder\Block;

interface FakesBlocks
{
    /**
     * @param  class-string<Block>  $block
     * @return array{type: string, data: array<string, mixed>}
     */
    public function fake(string $block, string $name): array;
}
