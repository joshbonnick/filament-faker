<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use UnhandledMatchError;

abstract class GeneratesFakes
{
    use InteractsWithFakeConfig;
    use GeneratesFakesFromComponentName;

    public function __construct()
    {
        $this->setUpConfig();
    }

    protected function mutate(Component|Form $parent, Field $component): mixed
    {
        if (method_exists($parent, 'mutateFake')) {
            try {
                $content = $parent->mutateFake($component);
            } catch (UnhandledMatchError $e) {
                return $component;
            }

            return (is_callable($content) ? $content($component) : $content) ?? $component;
        }

        return $component;
    }
}
