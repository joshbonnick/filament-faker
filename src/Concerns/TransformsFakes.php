<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\Fakers\FakesForms;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use ReflectionException;

/**
 * @internal
 */
trait TransformsFakes
{
    protected ?Closure $mutateCallback = null;

    protected bool $shouldFakeUsingComponentName = true;

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
     * Get mutation methods from given components parent.
     *
     * Returns the component if no mutate methods exist.
     */
    protected function getMutationsFromParent(Component|Form $parent, Field $component): mixed
    {
        try {
            return $this->resolveOrReturn(
                callback: [$parent, 'mutateFake'],
                parameters: [Field::class => $component, $component::class => $component]
            ) ?? $component;
        } catch (ReflectionException $e) {
            throw_unless(str_contains($e->getMessage(), 'mutateFake() does not exist'), $e);
        }

        return $component;
    }

    /**
     * Apply mutations applied to this instance to the new FilamentFaker instance.
     *
     * @template TReturnValue
     *
     * @param  TReturnValue  $to
     * @return TReturnValue
     */
    protected function applyFakerMutations($to)
    {
        $to->shouldFakeUsingComponentName($this->shouldFakeUsingComponentName);

        if ($this->usesFactory()) {
            $to->withFactory($this->getFactory(), $this->getOnlyFactoryAttributes());
        }

        if (filled($this->mutateCallback)) {
            $to->mutateFake($this->mutateCallback);
        }

        if (method_exists($this, 'getOnlyFields') && $to instanceof FakesForms) {
            $to->onlyFields(...$this->getOnlyFields());
        }

        return $to;
    }

    abstract protected function usesFactory(): bool;

    /**
     * @return ?Factory<Model>
     */
    abstract protected function getFactory(): ?Factory;

    abstract protected function getOnlyFactoryAttributes(): array;

    /**
     * @param  array<class-string|string, object>  $parameters
     */
    abstract protected function resolveOrReturn(mixed $callback, array $parameters): mixed;
}
