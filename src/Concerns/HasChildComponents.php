<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\Fakers\FakesComponents;

/**
 * @internal
 */
trait HasChildComponents
{
    /**
     * Attempt to apply mutations from the parent component instance before returning
     * the components faker response.
     */
    protected function getContentForChildComponent(Field $component, Component|Form $parent, ComponentContainer $container): mixed
    {
        $transformed = $this->getMutationsFromParent($parent, $component);

        if ($transformed instanceof Field) {
            return $this->getComponentFaker($transformed, $container)->fake();
        }

        return $transformed;
    }

    abstract protected function getComponentFaker(Field $component, ComponentContainer $container): FakesComponents;

    abstract protected function getMutationsFromParent(Component|Form $parent, Field $component): mixed;
}
