<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\GeneratesFakesFromComponentName;
use FilamentFaker\Concerns\InteractsWithFakeConfig;
use FilamentFaker\Contracts\FakesBlocks;
use Throwable;

class BlockFaker implements FakesBlocks
{
    use GeneratesFakesFromComponentName;
    use InteractsWithFakeConfig;

    protected Block $block;

    public function __construct()
    {
        $this->setUpConfig();
    }

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
                ->mapWithKeys(function (Field $component) {
                    try {
                        return [$component->getName() => $this->block->mutateFake($component)() ?? $component->fake()];
                    } catch (Throwable $e) {
                        return [$component->getName() => $component->fake()];
                    }
                })
                ->toArray(),
        ];
    }
}
