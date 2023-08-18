<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use FilamentFaker\Support\Livewire;

/**
 * @internal
 */
trait InteractsWithFilamentContainer
{
    public function setUpComponent(Field $component): Field
    {
        return tap($component)->container($this->container());
    }

    public function setUpBlock(Block $block): Block
    {
        return tap($block)->container($this->container());
    }

    protected function container(): ComponentContainer
    {
        return ComponentContainer::make(Livewire::make());
    }
}
