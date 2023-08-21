<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * @internal
 */
trait InteractsWithFactories
{
    /**
     * @var Factory<Model>|null
     */
    protected ?Factory $factory = null;

    protected ?Model $model = null;

    /**
     * @var array<string, mixed>
     */
    protected array $modelAttributes = [];

    /**
     * @var array<int, string>
     */
    protected array $onlyAttributes = [];

    /**
     * @return class-string<Model>|string
     */
    abstract public function resolveModel(): string;

    /**
     * Generate fake data using model factories.
     *
     * @param  array<int, string>  $onlyAttributes
     * @param  Factory<Model>|class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(Factory|string $factory = null, array $onlyAttributes = []): static
    {
        return tap($this, function () use ($factory, $onlyAttributes): void {
            $this->onlyAttributes = $onlyAttributes;

            if ($factory instanceof Factory) {
                $this->factory = $factory;

                return;
            }

            if (is_null($factory) && ! (isset($this->resource) || isset($this->form))) {
                throw new InvalidArgumentException('You must provide a Factory.');
            }

            try {
                if (filled($factory)) {
                    $this->factory = app($factory);

                    return;
                }
            } catch (BindingResolutionException $e) {
            }

            $model = $this->resolveModel();

            if (! method_exists($model, 'factory')) {
                throw new InvalidArgumentException("Unable to find Factory for $model.");
            }

            $this->factory = $model::factory();
        });
    }

    protected function getFactoryDefinition(string $key): mixed
    {
        return $this->modelAttributes[$key];
    }

    protected function usesFactory(): bool
    {
        return ! is_null($this->factory);
    }

    /**
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     */
    protected function getModelInstance(callable|array $attributes = []): ?Model
    {
        return tap($this->getFactory()?->makeOne($attributes), function (?Model $model) {
            $this->model = $model;
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function getModelAttributes(): array
    {
        $instance = $this->getModelInstance()?->toArray() ?? [];

        $attributes = empty($this->onlyAttributes)
            ? $instance
            : Arr::only($instance, $this->onlyAttributes);

        return tap($attributes, function (array $attributes) {
            $this->modelAttributes = $attributes;
        });
    }

    /**
     * @return array<int, string>
     */
    protected function getOnlyFactoryAttributes(): array
    {
        return $this->onlyAttributes;
    }

    /**
     * @return ?Factory<Model>
     */
    protected function getFactory(): ?Factory
    {
        return $this->factory;
    }
}
