<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\ResolvesClosures;
use FilamentFaker\Concerns\TransformsFakes;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Support\FakerFactory;

abstract class FilamentFaker
{
    use InteractsWithFactories;
    use TransformsFakes;
    use ResolvesClosures;

    protected function getFormFaker(Form $form): FakesForms
    {
        return tap(FakerFactory::from($this)->form($form), fn (FakesForms $faker) => $this->applyFakerMutations($faker));
    }

    protected function getComponentFaker(Field $component): FakesComponents
    {
        return tap(FakerFactory::from($this)->component($component), fn (FakesComponents $faker) => $this->applyFakerMutations($faker));
    }

    protected function getBlockFaker(Block $block): FakesBlocks
    {
        return tap(FakerFactory::from($this)->block($block), fn (FakesBlocks $faker) => $this->applyFakerMutations($faker));
    }
}
