<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\InteractsWithConfig;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Concerns\ResolvesClosures;
use FilamentFaker\Concerns\TransformsFakes;
use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FilamentFaker;
use FilamentFaker\Contracts\Support\DataGenerator;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use ReflectionException;

class ComponentFaker implements FakesComponents, FilamentFaker
{
    use InteractsWithFilamentContainer;
    use InteractsWithFactories;
    use InteractsWithConfig;
    use TransformsFakes;
    use ResolvesClosures;

    protected readonly ComponentDecorator $component;

    public function __construct(
        protected readonly DataGenerator $generator,
        ComponentDecorator $decorator,
        Field $field,
        ComponentContainer $container = null
    ) {
        $this->generator->uses(
            decorator: $decorator->uses(
                component: $field->container(
                    container: $container ?? $this->getContainer(from: $field)
                )
            )
        );

        $this->component = $decorator;
    }

    public function fake(): mixed
    {
        return $this->component
            ->setState($this->component->getState() ?? $this->resolveOrReturn($this->generate()))
            ->format();
    }

    /**
     * Strategy for data generation while prioritizing mutation
     * options before resorting to a backup generator.
     */
    protected function generate(): mixed
    {
        if (is_callable($this->mutateCallback)) {
            $data = $this->resolveOrReturn($this->mutateCallback);

            if (filled($data)) {
                return $data;
            }
        }

        if (! is_null($mutateCallbackResponse = $this->callComponentMutation())) {
            return $mutateCallbackResponse;
        }

        if ($this->factoryDefinitionExists()) {
            return $this->getFactoryDefinition(key: $this->component->getName());
        }

        if ($this->getShouldFakeUsingComponentName()) {
            $data = $this->generator->realTime()->generate($this->component());

            if (filled($data)) {
                return $data;
            }
        }

        if ($this->component->hasOverride()) {
            return $this->resolveOrReturn($this->config()[$this->component()::class]);
        }

        return $this->generator->generate();
    }

    protected function callComponentMutation(): mixed
    {
        try {
            return $this->resolveOrReturn([$this->component(), 'mutateFake']);
        } catch (ReflectionException $e) {
            throw_unless(str_contains($e->getMessage(), 'mutateFake() does not exist'));
        }

        return null;
    }

    protected function component(): Field
    {
        return $this->component->getField();
    }

    /**
     * @return array<class-string|string, object>
     */
    protected function injectionParameters(): array
    {
        return [
            Field::class => $this->component(),
            Component::class => $this->component(),
            $this->component()::class => $this->component(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function resolveModel(): string
    {
        return $this->component->getModel()
               ?? throw new InvalidArgumentException("Unable to find Model for [{$this->component->getName()}] component.");
    }

    protected function factoryDefinitionExists(): bool
    {
        return Arr::has($this->getModelAttributes(), $this->component()->getName());
    }

    /**
     * Resolve whether Faker should be using the components name for generating data.
     */
    protected function getShouldFakeUsingComponentName(): bool
    {
        if ($this->shouldFakeUsingComponentName === false) {
            return false;
        }

        return config('filament-faker.fake_using_component_name', true)
            && ! $this->component->hasOptions();
    }
}
