<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\ResolvesClosures;
use FilamentFaker\Concerns\ResolvesFakerInstances;
use FilamentFaker\Concerns\TransformsFakes;

abstract class FilamentFaker
{
    use InteractsWithFactories;
    use ResolvesFakerInstances;
    use TransformsFakes;
    use ResolvesClosures;
}
