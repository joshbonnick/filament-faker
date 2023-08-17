<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use FilamentFaker\Contracts\FakesResources;
use Illuminate\Support\Str;
use InvalidArgumentException;
use UnhandledMatchError;

abstract class GeneratesFakes
{
    use InteractsWithFakeConfig;
    use InteractsWithFactories;
    use MutatesFakes;

    protected Block $block;

    protected Field $component;

    protected bool $shouldFakeUsingComponentName = true;

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

    public function shouldFakeUsingComponentName(bool $should = true): static
    {
        return tap($this, function () use ($should) {
            $this->shouldFakeUsingComponentName = $should;
        });
    }

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

    protected function getFormFaker(Form $form): FakesForms
    {
        return tap($form->faker(), fn (FakesForms $faker) => $this->applyFakerMutations($faker));
    }

    protected function getComponentFaker(Field $component): FakesComponents
    {
        return tap($component->faker(), fn (FakesComponents $faker) => $this->applyFakerMutations($faker));
    }

    protected function getBlockFaker(Block $block): FakesBlocks
    {
        return tap($block->faker(), fn (FakesBlocks $faker) => $this->applyFakerMutations($faker));
    }

    protected function applyFakerMutations(GeneratesFakes $faker): void
    {
        $faker->shouldFakeUsingComponentName($this->shouldFakeUsingComponentName);

        if ($this->usesFactory()) {
            $faker->withFactory($this->factory, $this->onlyAttributes);
        }

        if ($this->hasMutations()) {
            $faker->mutateFake($this->mutateCallback);
        }
    }
}
