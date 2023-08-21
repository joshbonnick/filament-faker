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
        });
    }

    public function form(Form $form): FakesForms
    {
        return app(FakesForms::class, [
            'form' => $this->configure($form),
            'container' => $this->container,
        ]);
    }

    public function component(Field $component): FakesComponents
    {
        return app(FakesComponents::class, [
            'field' => $this->configure($component),
            'container' => $this->container,
        ]);
    }

    public function block(Block $block): FakesBlocks
    {
        return app(FakesBlocks::class, [
            'block' => $this->configure($block),
            'container' => $this->container,
        ]);
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
        })->model(rescue(fn (): string => $this->parentFaker->resolveModel()));
    }
}
