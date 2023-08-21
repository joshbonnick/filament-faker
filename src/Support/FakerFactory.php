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
use FilamentFaker\Contracts\Support\FilamentFakerFactory;
use FilamentFaker\Fakers\FilamentFaker;

class FakerFactory implements FilamentFakerFactory
{
    use InteractsWithFilamentContainer;

    protected FilamentFaker $parentFaker;

    public function from(FilamentFaker $parent): FakerFactory
    {
        return tap($this, function () use ($parent) {
            $this->parentFaker = $parent;
        });
    }

    public function form(Form $form): FakesForms
    {
        return $this->configure($form)->faker();
    }

    public function component(Field $component, ComponentContainer $container): FakesComponents
    {
        return app(FakesComponents::class, [
            'field' => $this->configure($component),
            'container' => $container,
        ]);
    }

    public function block(Block $block, ComponentContainer $container): FakesBlocks
    {
        return app(FakesBlocks::class, [
            'block' => $this->configure($block),
            'container' => $container,
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
