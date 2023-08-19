<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Fakers\FilamentFaker;

class FakerFactory
{
    public function __construct(protected FilamentFaker $from)
    {
    }

    public static function from(FilamentFaker $from): FakerFactory
    {
        return new FakerFactory($from);
    }

    public function form(Form $form): FakesForms
    {
        return $form->faker();
    }

    public function component(Field $component): FakesComponents
    {
        return $component->faker();
    }

    public function block(Block $block): FakesBlocks
    {
        return $block->faker();
    }
}
