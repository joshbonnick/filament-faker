<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\GeneratesFakes;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FilamentFaker;

class BlockFaker extends GeneratesFakes implements FakesBlocks, FilamentFaker
{
    public function __construct(protected Block $block)
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function fake(): array
    {
        return [
            'type' => $this->block::class,
            'data' => collect($this->block->getChildComponents())
                ->filter(fn (mixed $component) => $component instanceof Field)
                ->mapWithKeys(fn (Field $component) => [$component->getName() => $this->getContentForComponent($component, $this->block)])
                ->toArray(),
        ];
    }
}
