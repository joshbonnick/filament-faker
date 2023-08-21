<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use FilamentFaker\Concerns\InteractsWithConfig;
use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Support\DataGenerator;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use ReflectionException;

class ComponentFaker extends FilamentFaker implements FakesComponents
{
    use InteractsWithConfig;

    public function __construct(
        protected readonly DataGenerator $generator,
        protected readonly ComponentDecorator $component,
        Field $field,
    ) {
        $this->component->setUp($field);
        $this->generator->uses($this->component);
    }

    public function fake(): mixed
    {
        $data = $this->resolveOrReturn($this->generate());

        $this->component->setState($data);

        return $this->component->format();
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
