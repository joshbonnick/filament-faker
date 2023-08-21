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
use FilamentFaker\Contracts\Support\FilamentFakerFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

abstract class FilamentFaker
{
    use InteractsWithFactories;
    use TransformsFakes;
    use ResolvesClosures;

    protected function getFormFaker(Form $form): FakesForms
    {
        return tap($this->fakerFactory()->form($form), fn (FakesForms $faker) => $this->applyFakerMutations($faker));
    }

    protected function getComponentFaker(Field $component): FakesComponents
    {
        return tap($this->fakerFactory()->component($component, $this->container), fn (FakesComponents $faker) => $this->applyFakerMutations($faker));
    }

    protected function getBlockFaker(Block $block): FakesBlocks
    {
        return tap($this->fakerFactory()->block($block, $this->container), fn (FakesBlocks $faker) => $this->applyFakerMutations($faker));
    }

    protected function fakerFactory(): FilamentFakerFactory
    {
        return app(FilamentFakerFactory::class)->from(parent: $this);
    }

    /**
     * @return class-string<Model>|string
     *
     * @throws InvalidArgumentException
     *
     * @internal
     */
    abstract public function resolveModel(): string;
}
