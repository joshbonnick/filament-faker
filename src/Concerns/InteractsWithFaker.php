<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Field;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait InteractsWithFaker
{
    protected bool $shouldFakeUsingComponentName = true;

    /**
     * Resolve whether Faker should be using the components name for generating data.
     */
    protected function getShouldFakeUsingComponentName(Field $component): bool
    {
        if ($this->shouldFakeUsingComponentName === false) {
            return false;
        }

        $target = $this->component ?? $this->block;

        return method_exists($target, 'shouldFakeUsingComponentName')
            ? $target->shouldFakeUsingComponentName($component)
            : config('filament-faker.use_component_names_for_fake', true);
    }

    /**
     * Generate fake data using a FakerPHP method.
     *
     * @throws InvalidArgumentException
     */
    protected function fakeUsingComponentName(Field $component): mixed
    {
        if ($this->isDisabledFakerMethod($name = Str::camel($component->getName()))) {
            return null;
        }

        try {
            return fake()->$name;
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Check if component name is in the disabled faker method array.
     */
    protected function isDisabledFakerMethod(string $componentName): bool
    {
        $methods = $this->filteredFakerMethods();

        return in_array(Str::camel($componentName), $methods) || in_array(Str::snake($componentName), $methods);
    }

    /**
     * @return array<int, string>
     */
    private function filteredFakerMethods(): array
    {
        return config('filament-faker.slow_faker_methods', []);
    }
}
