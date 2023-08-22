<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use FilamentFaker\Support\Livewire;
use Throwable;

/**
 * @internal
 */
trait InteractsWithFilamentContainer
{
    protected function setUpBlock(Block $block): Block
    {
        return tap($block)->container($this->container());
    }

    protected function getContainer(Component $from = null): ComponentContainer
    {
        try {
            return $from?->getContainer() ?? $this->container ?? $this->container();
        } catch (Throwable $e) {
            throw_unless(str_contains($e->getMessage(), '$container'), $e);
        }

        return $this->container();
    }

    protected function container(): ComponentContainer
    {
        return ComponentContainer::make(Livewire::make());
    }
}
