<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\Fakers\FilamentFaker;
use FilamentFaker\Contracts\Support\FilamentFakerFactory;

/**
 * @internal
 */
trait HasChildComponents
{
    /** @var string[] */
    protected array $onlyFields = [];

    /**
     * {@inheritDoc}
     */
    public function onlyFields(string ...$fields): static
    {
        return tap($this, function () use ($fields) {
            $this->onlyFields = $fields;
        });
    }

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

    protected function faker(Form|Block|Field $item): FilamentFaker
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

    /**
     * @return string[]
     */
    protected function getOnlyFields(): array
    {
        return $this->onlyFields;
    }

    abstract protected function applyFakerMutations(FilamentFaker $to): FilamentFaker;

    abstract protected function getMutationsFromParent(Component|Form $parent, Field $component): mixed;
}
