<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * @internal
 */
trait InteractsWithFactories
{
    use InteractsWithFilamentContainer;

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
                throw new InvalidArgumentException('You must provide a factory.');
            }

            try {
                if (is_null($factory)) {
                    throw new BindingResolutionException();
                }

                $this->factory = resolve($factory);
            } catch (BindingResolutionException $e) {
                if (is_null($model = $this->resolveModel())) {
                    throw new InvalidArgumentException('Unable to find Model.');
                }

                if (! in_array(HasFactory::class, class_uses_recursive($model))) {
                    throw new InvalidArgumentException("Unable to find Factory for $model.");
                }

                $this->factory = $model::factory(); // @phpstan-ignore-line
            }

            if(!$this->factory){
                throw new InvalidArgumentException("Unable to resolve Factory.");
            }
        });
    }

    /**
     * @return class-string<Model>|string|null
     *
     * @throws InvalidArgumentException
     */
    protected function resolveModel(): ?string
    {
        return match (true) {
            isset($this->component) => $this->component->getModel(),
            isset($this->form) => $this->form->getModel(),
            isset($this->resource) => $this->resource::getModel(),
            default => throw new InvalidArgumentException('Unable to resolve Model.')
        };
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
     * @return ?Factory<Model>
     */
    protected function getFactory(): ?Factory
    {
        return $this->factory;
    }
}
