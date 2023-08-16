<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use InvalidArgumentException;

trait GeneratesFakesFromComponentName
{
    protected Block $block;

    protected Field $component;

    protected function shouldFakeUsingComponentName(Field $component): bool
    {
        $target = $this->component ?? $this->block;

        return method_exists($target, 'shouldFakeUsingComponentName')
            ? $target->shouldFakeUsingComponentName($component)
            : config('filament-faker.use_component_names_for_fake', true);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function fakeUsingComponentName(Field $component): mixed
    {
        if ($this->isDisabledFakerMethod($name = str($component->getName())->camel()->toString())) {
            return null;
        }

        try {
            return fake()->$name;
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    protected function isDisabledFakerMethod(string $componentName): bool
    {
        return in_array($componentName, $this->filteredFakerMethods());
    }

    /**
     * @return array<int, string>
     */
    private function filteredFakerMethods(): array
    {
        return config('filament-faker.slow_faker_methods', []);
    }
}
