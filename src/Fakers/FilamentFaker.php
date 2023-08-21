<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Concerns\ResolvesClosures;
use FilamentFaker\Concerns\TransformsFakes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @internal
 */
abstract class FilamentFaker
{
    use InteractsWithFactories;
    use TransformsFakes;
    use ResolvesClosures;
    use InteractsWithFilamentContainer;

    /**
     * @return class-string<Model>|string
     *
     * @throws InvalidArgumentException
     *
     * @internal
     */
    abstract public function resolveModel(): string;
}
