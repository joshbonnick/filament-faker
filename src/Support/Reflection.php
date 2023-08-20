<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use FilamentFaker\Contracts\Support\Reflectable;
use ReflectionException;
use ReflectionProperty;

/**
 * @internal
 */
class Reflection implements Reflectable
{
    protected object $object;

    public function reflect(object $object): static
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
