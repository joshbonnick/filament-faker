<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\GeneratesFakes;
use FilamentFaker\Contracts\FakesBlocks;

class BlockFaker extends GeneratesFakes implements FakesBlocks
{
    protected Block $block;

    /**
     * {@inheritDoc}
     */
    public function fake(Block $block): array
    {
        $this->block = $block;

        return [
            'type' => $this->block::class,
            'data' => collect($this->block->getChildComponents())
                ->filter(fn (mixed $component) => $component instanceof Field)
                ->mapWithKeys(fn (Field $component) => [$component->getName() => $this->getContentForComponent($component)])
                ->toArray(),
        ];
    }

    protected function getContentForComponent(Field $component): mixed
    {
        return ($content = $this->mutate($this->block, $component)) instanceof Field
            ? $content->fake() // @phpstan-ignore-line
            : $content;
    }
}
