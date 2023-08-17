<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Field;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait InteractsWithFactories
{
    protected Field $component;

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

            try {
                if (! is_null($factory)) {
                    $this->factory = resolve($factory);
                }
            } catch (BindingResolutionException $e) {
                if (is_null($model = $this->component->getModel())) {
                    throw new InvalidArgumentException("Unable to find Model for {$this->component->getName()}");
                }

                if (! in_array(HasFactory::class, class_uses_recursive($model))) {
                    throw new InvalidArgumentException("Unable to find Factory for $model");
                }

                $this->factory = $model::factory();
            }
        });
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
        if ($this->model) {
            return $this->model;
        }

        return $this->factory?->makeOne($attributes);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getModelAttributes(): array
    {
        if ($this->modelAttributes) {
            return $this->modelAttributes;
        }

        $instance = $this->getModelInstance()?->toArray() ?? [];

        return ! empty($this->onlyAttributes)
            ? Arr::only($instance, $this->onlyAttributes)
            : $instance;
    }

    /**
     * @return ?Factory<Model>
     */
    protected function getFactory(): ?Factory
    {
        return $this->factory;
    }
}
