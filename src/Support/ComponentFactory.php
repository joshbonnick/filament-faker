<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Set;
use FilamentFaker\Concerns\InteractsWithConfig;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Contracts\ComponentAPI;
use Illuminate\Support\Arr;
use ReflectionException;
use ReflectionProperty;

/**
 * @internal
 */
class ComponentFactory implements ComponentAPI
{
    use InteractsWithConfig;
    use InteractsWithFilamentContainer;

    public Field $component;

    public function setUp(Field $component): Field
    {
        return tap($this->setUpComponent($component), function (Field $component) {
            $this->component = $component;
        });
    }

    public function component(): Field
    {
        return $this->component;
    }

    public function canBeDateFormatted(): bool
    {
        return is_a($this->component, DateTimePicker::class);
    }

    public function setState(mixed $state): static
    {
        return tap($this, function () use ($state) {
            $this->component->state(fn (Set $set) => $set($this->component->getName(), $state));
        });
    }

    public function getAfterStateHydrated(): ?Closure
    {
        try {
            $afterStateHydrated = tap(new ReflectionProperty($this->component, 'afterStateHydrated'))
                ->setAccessible(true);

            return $afterStateHydrated->getValue($this->component);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    public function hasOptions(): bool
    {
        return method_exists($this->component, 'getOptions');
    }

    public function hasOverride(): bool
    {
        return Arr::has($this->config(), $this->component::class);
    }
}
