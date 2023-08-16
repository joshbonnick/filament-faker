<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Field;
use InvalidArgumentException;

trait GeneratesFakeFromComponentName
{
    /**
     * @throws InvalidArgumentException
     */
    protected function fakeUsingComponentName(Field $component): mixed
    {
        if (in_array($name = str($component->getName())->camel(), $this->filteredFakerMethods())) {
            throw new InvalidArgumentException("$name is a disabled method in config.");
        }

        return fake()->{str($component->getName())->camel()};
    }

    protected function shouldFakeUsingComponentName(Field $component): bool
    {
        return method_exists($this->block, 'shouldFakeUsingComponentName')
            ? $this->block->shouldFakeUsingComponentName($component)
            : config('filament-faker.use_component_names_for_fake', true);
    }

    /**
     * @return array<int, string>
     */
    private function filteredFakerMethods(): array
    {
        return config('filaments-faker.slow_faker_methods', []);
    }
}
