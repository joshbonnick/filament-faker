<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\FakesBlocks;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\FakesForms;
use FilamentFaker\Contracts\FilamentFaker;

trait ResolvesFakerInstances
{
    protected function getFormFaker(Form $form): FakesForms
    {
        return tap($form->faker(), fn (FilamentFaker $faker) => $this->applyFakerMutations($faker));
    }

    protected function getComponentFaker(Field $component): FakesComponents
    {
        return tap($component->faker(), fn (FilamentFaker $faker) => $this->applyFakerMutations($faker));
    }

    protected function getBlockFaker(Block $block): FakesBlocks
    {
        return tap($block->faker(), fn (FilamentFaker $faker) => $this->applyFakerMutations($faker));
    }

    /**
     * Apply mutations applied to this instance to the new Faker instance.
     */
    protected function applyFakerMutations(FilamentFaker $faker): void
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
