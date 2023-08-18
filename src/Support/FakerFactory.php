<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Faker\Generator;
use FilamentFaker\Contracts\RealTimeFactory;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FakerFactory implements RealTimeFactory
{
    protected readonly Generator $faker;

    public function __construct()
    {
        $this->faker = fake();
    }

    public function fakeFromName(string $name): mixed
    {
        if ($this->isDisabledFakerMethod($name = Str::camel($name))) {
            return null;
        }

        try {
            return $this->faker->$name;
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
