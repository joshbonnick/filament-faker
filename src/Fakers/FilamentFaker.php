<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\ResolvesFakerInstances;
use FilamentFaker\Concerns\TransformsFakes;
use Illuminate\Support\Facades\App;

abstract class FilamentFaker
{
    use InteractsWithFactories;
    use ResolvesFakerInstances;
    use TransformsFakes;

    /**
     * @template TReturnType
     *
     * @param  TReturnType|callable(): TReturnType  $callback
     * @param  array<class-string|string, object>  $parameters
     * @return ?TReturnType
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
