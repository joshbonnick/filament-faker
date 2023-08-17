<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;

trait InteractsWithFactories
{
    protected function getFactory(): Factory
    {
        return $this->component->getModel()::factory();
    }
}
