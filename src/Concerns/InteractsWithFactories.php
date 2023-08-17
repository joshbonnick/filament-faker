<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Field;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait InteractsWithFactories
{
    protected Field $component;

    /**
     * @var Factory<Model>|null
     */
    protected ?Factory $factory = null;

    /**
     * @param  Factory<Model>|class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(Factory|string $factory = null): static
    {
        return tap($this, function () use ($factory): void {
            if ($factory instanceof Factory) {
                $this->factory = $factory;

                return;
            }

            try {
                if (! is_null($factory)) {
                    $this->factory = resolve($factory);

                    return;
                }
            } catch (BindingResolutionException $e) {
            }

            if (is_null($model = $this->component->getModel())) {
                throw new InvalidArgumentException("Unable to find Model for {$this->component->getName()}");
            }

            if (! in_array(HasFactory::class, class_uses_recursive($model))) {
                throw new InvalidArgumentException("Unable to find Factory for $model");
            }

            $this->factory = $model::factory();
        });
    }

    /**
     * @param  (callable(array<string, mixed>): array<string, mixed>)|array<string, mixed>  $attributes
     */
    protected function getModelInstance(callable|array $attributes = []): ?Model
    {
        return $this->factory?->makeOne($attributes);
    }

    /**
     * @return ?Factory<Model>
     */
    protected function getFactory(): ?Factory
    {
        return $this->factory;
    }
}
