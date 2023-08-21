<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Contracts\Support\FilamentFakerFactory;

/**
 * @internal
 */
trait HasChildComponents
{
    /**
     * Attempt to apply mutations from the parent component instance before returning
     * the components faker response.
     */
    protected function getContentForChildComponent(Field $component, Component|Form $parent): mixed
    {
        $transformed = $this->getMutationsFromParent($parent, $component);

        if ($transformed instanceof Field) {
            return $this->faker($transformed)->fake();
        }

        return $transformed;
    }

    protected function faker(Form|Block|Field $item): FakesForms|FakesComponents|FakesBlocks
    {
        $method = match (true) {
            $item instanceof Form => 'form',
            $item instanceof Field => 'component',
            $item instanceof Block => 'block',
        };

        return $this->applyFakerMutations(to: $this->factory()->{$method}($item));
    }

    protected function factory(): FilamentFakerFactory
    {
        return app(FilamentFakerFactory::class)->from(parent: $this, container: $this->getContainer());
    }

    abstract protected function applyFakerMutations($to);

    abstract protected function getMutationsFromParent(Component|Form $parent, Field $component): mixed;
}
