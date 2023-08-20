<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Contracts\Support\FilamentFakerFactory;
use FilamentFaker\Fakers\FilamentFaker;

class FakerFactory implements FilamentFakerFactory
{
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

    public function component(Field $component): FakesComponents
    {
        return $this->configure($component)->faker();
    }

    public function block(Block $block): FakesBlocks
    {
        return $this->configure($block)->faker();
    }

    /**
     * @template TReturnValue
     *
     * @params TReturnValue $component
     *
     * @return TReturnValue
     */
    protected function configure($component)
    {
        return $component->configure()->model(rescue(fn (): string => $this->parentFaker->resolveModel()));
    }
}
