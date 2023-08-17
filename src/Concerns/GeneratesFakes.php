<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use InvalidArgumentException;
use UnhandledMatchError;

abstract class GeneratesFakes
{
    use InteractsWithFakeConfig;

    public function __construct()
    {
        $this->setUpConfig();
    }

    protected function mutate(Component|Form $parent, Field $component): mixed
    {
        if (method_exists($parent, 'mutateFake')) {
            try {
                $content = $parent->mutateFake($component);
            } catch (UnhandledMatchError $e) {
                return $component;
            }

            return (is_callable($content) ? $content($component) : $content) ?? $component;
        }

        return $component;
    }

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
