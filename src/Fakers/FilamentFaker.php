<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Concerns\ResolvesClosures;
use FilamentFaker\Concerns\TransformsFakes;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Contracts\Support\FilamentFakerFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @internal
 */
abstract class FilamentFaker
{
    use InteractsWithFactories;
    use TransformsFakes;
    use ResolvesClosures;
    use InteractsWithFilamentContainer;

    protected function formFaker(Form $form): FakesForms
    {
        return $this->applyFakerMutations(to: $this->factory()->form($form));
    }

    protected function componentFaker(Field $component): FakesComponents
    {
        return $this->applyFakerMutations(to: $this->factory()->component($component));
    }

    protected function blockFaker(Block $block): FakesBlocks
    {
        return $this->applyFakerMutations(to: $this->factory()->block($block));
    }

    protected function factory(): FilamentFakerFactory
    {
        return app(FilamentFakerFactory::class)->from(parent: $this, container: $this->getContainer());
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
