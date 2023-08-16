<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\GeneratesFakesFromComponentName;
use FilamentFaker\Concerns\InteractsWithFakeConfig;
use FilamentFaker\Contracts\FakesBlocks;

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

                    if (method_exists($this->block, 'mutateFake')) {
                        $content = $this->block->mutateFake($component);

                        if (is_callable($content)) {
                            $content = $content($component);
                        }
                    }

                    $content ??= $component->fake();

                    return [$component->getName() => $content];
                })
                ->toArray(),
        ];
    }
}
