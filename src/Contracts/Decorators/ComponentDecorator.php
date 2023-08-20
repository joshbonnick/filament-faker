<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts\Decorators;

use Filament\Forms\Components\Field;

/**
 * @internal
 *
 * @mixin Field
 */
interface ComponentDecorator
{
    /**
     * @param  array<array-key>  $arguments
     */
    public function __call(string $name, array $arguments): mixed;

    public function __get(string $name): mixed;

    public function setUp(Field $component): Field;

    public function getField(): Field;

    public function format(): mixed;

    /**
     * @param  class-string<Field>  ...$classes
     */
    public function is_a(string ...$classes): bool;

    public function setState(mixed $state): static;

    public function getAfterStateHydrated(mixed $state): mixed;

    public function getAfterStateUpdated(mixed $state): mixed;

    public function hasOptions(): bool;

    public function hasOverride(): bool;

    public function isMultiple(): bool;

    public function hasMethod(string $method): bool;

    public function missingMethod(string $method): bool;
}
