<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use UnhandledMatchError;

trait TransformsFakes
{
    protected ?Closure $mutateCallback = null;

    /**
     * Faker should use the components name to retrieve a method from FakerPHP
     * to generate data?
     */
    public function shouldFakeUsingComponentName(bool $should = true): static
    {
        return tap($this, function () use ($should) {
            $this->shouldFakeUsingComponentName = $should;
        });
    }

    /**
     * Apply a mutation callback to the Faker instance.
     */
    public function mutateFake(Closure $callback = null): static
    {
        return tap($this, function () use ($callback) {
            $this->mutateCallback = $callback;
        });
    }

    /**
     * Mutate callback has been set.
     */
    protected function hasMutations(): bool
    {
        return ! is_null($this->mutateCallback);
    }

    /**
     * Get mutation methods from given components parent.
     *
     * Returns the component if no mutate methods exist.
     */
    protected function getMutationsFromParent(Component|Form $parent, Field $component): mixed
    {
        if (method_exists($parent, 'mutateFake')) {
            try {
                $content = $parent->mutateFake($component);
            } catch (UnhandledMatchError $e) {
                return $component;
            }

            return (is_callable($content) ? $content($component) : $content) ?? $component;
        }

        return $component;
    }
}