<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Concerns\ResolvesFakerInstances;
use FilamentFaker\Concerns\TransformsFakes;

abstract class GeneratesFakes
{
    use ResolvesFakerInstances;
    use InteractsWithFactories;
    use TransformsFakes;
    use InteractsWithFilamentContainer;

    /**
     * @var array<string|class-string<Field>, Closure>
     */
    protected array $fakesConfig;

    protected bool $shouldFakeUsingComponentName = true;

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

    /**
     * @return array<string|class-string<Field>, Closure>
     */
    protected function config(): array
    {
        return $this->fakesConfig ?? tap(config('filament-faker.fakes', []), function (array $config) {
            $this->fakesConfig = $config;
        });
    }
}
