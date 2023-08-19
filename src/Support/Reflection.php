<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use ReflectionException;
use ReflectionProperty;

/**
 * @internal
 */
class Reflection
{
    protected object $object;

    public function reflect(object $object): self
    {
        return tap($this, fn () => $this->object = $object);
    }

    /**
     * @throws ReflectionException
     */
    public function property(string $property): mixed
    {
        /** @var ReflectionProperty $reflection */
        $reflection = tap(new ReflectionProperty($this->object, $property))->setAccessible(true);

        return $reflection->getValue($this->object);
    }
}