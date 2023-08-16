<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\GeneratesFakes;
use FilamentFaker\Contracts\FakesBlocks;

class BlockFaker extends GeneratesFakes implements FakesBlocks
{
    protected Block $block;

    /**
     * {@inheritDoc}
     */
    public function fake(string $block, string $name): array
    {
        $this->block = $block::make($name);

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
        if (method_exists($this->block, 'mutateFake')) {
            $content = $this->block->mutateFake($component);

            if (is_callable($content)) {
                $content = $content($component);
            }
        }

        return $content ?? $component->fake();
    }
}
