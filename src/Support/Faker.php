<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Filament\Forms\Components\Field;
use FilamentFaker\Contracts\Support\RealTimeFactory;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Faker implements RealTimeFactory
{
    public function generate(Field $component): mixed
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

        return in_array($componentName, $methods) || in_array(Str::snake($componentName), $methods);
    }

    /**
     * @return array<int, string>
     */
    protected function filteredFakerMethods(): array
    {
        return config('filament-faker.excluded_faker_methods', []);
    }
}
