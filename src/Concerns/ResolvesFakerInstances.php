<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;

trait ResolvesFakerInstances
{
    protected bool $shouldFakeUsingComponentName = true;

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
        $faker->shouldFakeUsingComponentName($this->shouldFakeUsingComponentName);

        if ($this->usesFactory()) {
            $faker->withFactory($this->factory, $this->onlyAttributes);
        }

        if ($this->hasMutations()) {
            $faker->mutateFake($this->mutateCallback);
        }
    }
}
