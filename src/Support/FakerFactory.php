<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Contracts\Fakers\FilamentFaker;
use FilamentFaker\Contracts\Support\FilamentFakerFactory;

class FakerFactory implements FilamentFakerFactory
{
    use InteractsWithFilamentContainer;

    protected FilamentFaker $parentFaker;

    protected ?ComponentContainer $container = null;

    public function from(FilamentFaker $parent, ComponentContainer $container): static
    {
        return tap($this, function () use ($parent, $container) {
            [$this->parentFaker, $this->container] = [$parent, $container];

            app()->instance(ComponentContainer::class, $this->container);
        });
    }

    public function form(Form $form): FakesForms
    {
        app()->instance(Form::class, $this->configure($form));

        return tap(app(FakesForms::class), function () {
            $this->forgetContainer();
        });
    }

    public function component(Field $component): FakesComponents
    {
        app()->instance(Field::class, $this->configure($component));

        return tap(app(FakesComponents::class), function () {
            $this->forgetContainer();
        });
    }

    public function block(Block $block): FakesBlocks
    {
        app()->instance(Block::class, $this->configure($block));

        return tap(app(FakesBlocks::class), function () {
            $this->forgetContainer();
        });
    }

    /**
     * @template TReturnValue
     *
     * @param  TReturnValue  $component
     * @return TReturnValue
     */
    protected function configure($component)
    {
        return tap($component->configure(), function () use ($component) {
            if (method_exists($component, 'container')) {
                $component->container($this->getContainer(from: $component));
            }

            if ($model = rescue(fn () => $this->parentFaker->resolveModel())) {
                $component->model($model);
            }
        });
    }

    protected function forgetContainer(): void
    {
        app()->forgetInstance(ComponentContainer::class);
    }
}
