<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use FilamentFaker\Concerns\InteractsWithConfig;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Concerns\ResolvesFakerInstances;
use FilamentFaker\Concerns\TransformsFakes;
use Illuminate\Support\Facades\App;

abstract class FilamentFaker
{
    use InteractsWithFilamentContainer;
    use InteractsWithFactories;
    use InteractsWithConfig;
    use ResolvesFakerInstances;
    use TransformsFakes;

    /**
     * Attempt to apply mutations from the parent component instance before returning
     * the components faker response.
     */
    protected function getContentForChildComponent(Field $component, Component|Form $parent): mixed
    {
        $transformed = $this->getMutationsFromParent($parent, $component);

        if ($transformed instanceof Field) {
            return $this->getComponentFaker($transformed)->fake();
        }

        return $transformed;
    }

    /**
     * @param  array<class-string|string, object>  $parameters
     */
    protected function resolveOrReturn(mixed $callback, array $parameters = []): mixed
    {
        if (is_callable($callback)) {
            return $this->resolveOrReturn(
                callback: App::call($callback, $parameters = [...$this->injectionParameters(), ...$parameters]),
                parameters: $parameters
            );
        }

        return $callback;
    }

    /**
     * @return array<class-string|string, object>
     */
    abstract protected function injectionParameters(): array;
}
