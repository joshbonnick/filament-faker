<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Contracts\FakesBlocks;

class BlockFaker extends FilamentFaker implements FakesBlocks
{
    public function __construct(protected Block $block)
    {
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
                ->mapWithKeys(fn (Field $component) => [$component->getName() => $this->getContentForChildComponent($component, $this->block)])
                ->toArray(),
        ];
    }

    protected function resolveModel(): ?string
    {
        return $this->setUpBlock($this->block)->getModel();
    }

    /**
     * @return array<class-string|string, object>
     */
    protected function injectionParameters(): array
    {
        return [
            Block::class => $this->block,
            $this->block::class => $this->block,
        ];
    }
}
