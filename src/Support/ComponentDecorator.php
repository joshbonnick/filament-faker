<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DateTimeInterface;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Set;
use FilamentFaker\Concerns\InteractsWithConfig;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use ReflectionException;
use ReflectionProperty;

/**
 * @internal
 */
class ComponentDecorator
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

    public function format(): mixed
    {
        $state = $this->component->getState();

        if ($this->canBeDateFormatted()) {
            return $this->formatDate($state);
        }

        if ($this->component instanceof BaseFileUpload) {
            return $state;
        }

        return $this->applyFormattingHooks($state);
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

    public function afterStateHydrated(mixed $state): mixed
    {
        try {
            $afterStateHydrated = tap(new ReflectionProperty($this->component, 'afterStateHydrated'))
                ->setAccessible(true);

            return $this->component->evaluate($afterStateHydrated->getValue($this->component))
                   ?? $state;
        } catch (ReflectionException $e) {
            return null;
        }
    }

    public function afterStateUpdated(mixed $state): mixed
    {
        try {
            $afterStateUpdated = tap(new ReflectionProperty($this->component, 'afterStateUpdated'))
                ->setAccessible(true);

            return $this->component->evaluate($afterStateUpdated->getValue($this->component), ['old' => $state])
                   ?? $state;
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

    protected function applyFormattingHooks(mixed $state): mixed
    {
        $newState = $this->afterStateUpdated($this->afterStateHydrated($state));

        if ($newState instanceof Field) {
            return $newState->getState();
        }

        return $newState ?? $state;
    }

    protected function formatDate(DateTimeInterface|string $date): string
    {
        if (! method_exists($this->component, 'getFormat')) {
            throw new InvalidArgumentException("{$this->component->getName()} cannot be formatted into a date.");
        }

        try {
            return Carbon::parse($date)->format($this->component->getFormat());
        } catch (InvalidFormatException $e) {
            throw new InvalidArgumentException("{$this->component->getName()} cannot be formatted into a date.");
        }
    }
}
