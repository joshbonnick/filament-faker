<?php

namespace FilamentFaker\Contracts\Support;

use ReflectionException;

/**
 * @internal
 */
interface Reflectable
{
    public function reflect(object $object): static;

    /**
     * @throws ReflectionException
     */
    public function property(string $property): mixed;
}
