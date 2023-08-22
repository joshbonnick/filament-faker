<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Stringable;

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
    public function withFactory(Factory|string|Stringable $factory = null, array $onlyAttributes = []): static
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
                    $this->factory = app((string) $factory);

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

    protected function getFactoryDefinition(string|Stringable $key): mixed
    {
        return $this->getModelAttributes()[(string) $key];
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
        return $this->model ?? tap($this->getFactory()?->makeOne($attributes), function (?Model $model) {
            $this->model = $model;
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function getModelAttributes(): array
    {
        $instance = $this->getModelInstance()?->toArray() ?? [];

        return empty($this->onlyAttributes)
            ? $instance
            : Arr::only($instance, $this->onlyAttributes);
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
