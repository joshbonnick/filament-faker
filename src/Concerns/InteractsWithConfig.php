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
