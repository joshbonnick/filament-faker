<?php

declare(strict_types=1);

namespace FilamentFaker\Exceptions;

use Exception;
use Filament\Forms\Components\Field;
use Spatie\Ignition\Contracts\BaseSolution;
use Spatie\Ignition\Contracts\ProvidesSolution;
use Spatie\Ignition\Contracts\Solution;

class InvalidComponentOptionsException extends Exception implements ProvidesSolution
{
    /**
     * @var array<int, string>
     */
    public const ERROR_MSG = [
        100 => ':component is required. Options array is empty.',
        101 => ':component is required. Options and search array is empty.',
    ];

    public function __construct(
        protected readonly Field $component,
        int $code = 0
    ) {
        parent::__construct(
            message: str_replace(':component', $this->component->getName(), self::ERROR_MSG[$code]),
            code: $code
        );
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('');
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, Field>
     */
    public function context(): array
    {
        return ['component' => $this->component];
    }
}
