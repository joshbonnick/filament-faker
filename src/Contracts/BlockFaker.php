<?php

declare(strict_types=1);

namespace JoshBonnick\FilamentBlockFaker\Contracts;

use Filament\Forms\Components\Builder\Block;

interface BlockFaker
{
    /**
     * @param  class-string<Block>  $block
     * @return array{type: string, data: array<string, mixed>}
     */
    public function fake(string $block, string $name): array;
}
