<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;

abstract class GeneratesFakes
{
    use ResolvesFakerInstances;
    use InteractsWithFakeConfig;
    use InteractsWithFactories;
    use MutatesFakes;
    use InteractsWithFilamentContainer;
    use InteractsWithFaker;

    protected Block $block;

    protected Field $component;

    public function __construct()
    {
        $this->setUpConfig();
    }

    /**
     * Attempt to apply mutations from the parent component instance before returning
     * the components faker response.
     */
    protected function getContentForComponent(Field $component, Component|Form $parent): mixed
    {
        if (! ($content = $this->getMutationsFromParent($parent, $component)) instanceof Field) {
            return $content;
        }

        return $this->getComponentFaker($content)->fake();
    }
}
