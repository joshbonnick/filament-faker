<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\HasChildComponents;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use InvalidArgumentException;

class BlockFaker extends FilamentFaker implements FakesBlocks
{
    use HasChildComponents;

    protected ComponentContainer $container;

    public function __construct(
        protected Block $block,
        ComponentContainer $container = null
    ) {
        $this->container = $container ?? $this->container();
    }

    /**
     * {@inheritDoc}
     */
    public function fake(): array
    {
        return [
            'type' => $this->block->getName(),
            'data' => collect($this->block->getChildComponents())
                ->filter(fn (mixed $component) => $component instanceof Field)
                ->mapWithKeys(fn (Field $component) => [$component->getName() => $this->getContentForChildComponent($component, $this->block)])
                ->toArray(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function resolveModel(): string
    {
        return $this->setUpBlock($this->block)->getModel()
               ?? throw new InvalidArgumentException("Unable to find Model for [{$this->block->getName()}] block.");
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
