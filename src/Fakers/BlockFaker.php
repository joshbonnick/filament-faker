<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\GeneratesFakes;
use FilamentFaker\Contracts\FakeBuilder;
use FilamentFaker\Contracts\FakesBlocks;

class BlockFaker extends GeneratesFakes implements FakesBlocks, FakeBuilder
{
    public function __construct(protected Block $block)
    {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    public function fake(): array
    {
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
