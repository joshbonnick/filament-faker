<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

abstract class GeneratesFakes
{
    use InteractsWithFakeConfig;
    use GeneratesFakesFromComponentName;

    public function __construct()
    {
        $this->setUpConfig();
    }
}
