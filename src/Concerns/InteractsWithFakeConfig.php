<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Closure;
use Filament\Forms\Components\Field;

trait InteractsWithFakeConfig
{
    /**
     * @var array<string|class-string<Field>, Closure>
     */
    protected array $fakesConfig;

    /**
     * @return array<string|class-string<Field>, Closure>
     */
    protected function setUpConfig(): array
    {
        return tap(config('filament-faker.fakes', []), function (array $config) {
            $this->fakesConfig = $config;
        });
    }
}
