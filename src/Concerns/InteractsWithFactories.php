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
     * @param  class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(string $factory = null): static
    {
        return tap($this, function () use ($factory): void {
            try {
                if (! is_null($factory)) {
                    $this->factory = resolve($factory);

                    return;
                }

                if (is_null($model = $this->component->getModel())) {
                    throw new InvalidArgumentException("Unable to find Model for {$this->component->getName()}");
                }

                if (! in_array(HasFactory::class, class_uses_recursive($model))) {
                    throw new InvalidArgumentException("Unable to find Factory for $model");
                }

                $this->factory = $model::factory();
            } catch (BindingResolutionException $e) {
            }
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
