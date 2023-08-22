<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Closure;
use Filament\Forms\Components\Field;

/**
 * @internal
 */
trait InteractsWithConfig
{
    /**
     * @var array<string|class-string<Field>, Closure>
     */
    protected array $fakesConfig;

    protected function config(string $path = null): mixed
    {
        $config = $this->fakesConfig ?? tap(config('filament-faker.fakes', []), function (array $config) {
            $this->fakesConfig = $config;
        });

        return $path ? data_get($config, $path) : $config;
    }
}
