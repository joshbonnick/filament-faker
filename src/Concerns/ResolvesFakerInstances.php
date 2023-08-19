<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Closure;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @internal
 */
trait ResolvesFakerInstances
{
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

    /**
     * Apply mutations applied to this instance to the new Faker instance.
     */
    protected function applyFakerMutations(FakesBlocks|FakesComponents|FakesForms $faker): void
    {
        $faker->shouldFakeUsingComponentName($this->getShouldFakeUsingComponentName());

        if ($this->usesFactory()) {
            $faker->withFactory($this->getFactory(), $this->getOnlyFactoryAttributes());
        }

        if ($this->hasMutations()) {
            $faker->mutateFake($this->getMutateCallback());
        }
    }

    abstract protected function hasMutations(): bool;

    abstract protected function getShouldFakeUsingComponentName(): bool;

    abstract protected function getMutateCallback(): ?Closure;

    abstract protected function usesFactory(): bool;

    /**
     * @return ?Factory<Model>
     */
    abstract protected function getFactory(): ?Factory;

    abstract protected function getOnlyFactoryAttributes(): array;
}
