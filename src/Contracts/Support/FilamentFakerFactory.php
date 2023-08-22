<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Support;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Resources\Resource as FilamentResource;
use FilamentFaker\Contracts\Fakers\FakesBlocks;
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Contracts\Fakers\FakesResources;
use FilamentFaker\Contracts\Fakers\FilamentFaker;

interface FilamentFakerFactory
{
    public function from(FilamentFaker $parent, ComponentContainer $container): static;

    public function form(Form $form): FakesForms;

    public function component(Field $component): FakesComponents;

    /**
     * @param  FilamentResource|class-string<FilamentResource>  $resource
     */
    public function resource(FilamentResource|string $resource): FakesResources;

    public function block(Block $block): FakesBlocks;
}
