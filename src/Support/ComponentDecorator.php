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

/**
 * @internal
 *
 * @mixin Field
 */
class ComponentDecorator
{
    use InteractsWithConfig;
    use InteractsWithFilamentContainer;

    public Field $component;

    public function __construct(
        protected Reflection $reflection
    ) {
    }

    /**
     * @param  array<array-key>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->component->$name(...$arguments);
    }

    public function __get(string $name): mixed
    {
        return $this->component->$name;
    }

    public function setUp(Field $component): Field
    {
        return tap($this->setUpComponent($component), function (Field $component) {
            $this->component = $component;
        });
    }

    public function getField(): Field
    {
        return $this->component;
    }

    public function format(): mixed
    {
        $state = $this->getState();

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
            $this->state(fn (Set $set) => $set($this->getName(), $state));
        });
    }

    public function getAfterStateHydrated(mixed $state): mixed
    {
        try {
            return $this->evaluate($this->reflect()->property('afterStateHydrated'))
                   ?? $state;
        } catch (ReflectionException $e) {
            throw_unless(str_contains($e->getMessage(), 'afterStateHydrated does not exist'), $e);
        }

        return null;
    }

    public function getAfterStateUpdated(mixed $state): mixed
    {
        try {
            return $this->evaluate($this->reflect()->property('afterStateUpdated'), ['old' => $state])
                   ?? $state;
        } catch (ReflectionException $e) {
            throw_unless(str_contains($e->getMessage(), 'afterStateUpdated does not exist'), $e);
        }

        return null;
    }

    public function hasOptions(): bool
    {
        return method_exists($this->component, 'getOptions');
    }

    public function hasOverride(): bool
    {
        return Arr::has($this->config(), $this->component::class);
    }

    protected function reflect(): Reflection
    {
        return $this->reflection->reflect($this->component);
    }

    protected function applyFormattingHooks(mixed $state): mixed
    {
        $newState = $this->getAfterStateUpdated($this->getAfterStateHydrated($state));

        if ($newState instanceof Field) {
            return $newState->getState();
        }

        return $newState ?? $state;
    }

    protected function formatDate(DateTimeInterface|string $date): string
    {
        if (! method_exists($this->component, 'getFormat')) {
            throw new InvalidArgumentException("{$this->getName()} cannot be formatted into a date.");
        }

        try {
            return Carbon::parse($date)->format($this->component->getFormat());
        } catch (InvalidFormatException $e) {
            throw new InvalidArgumentException("{$this->getName()} cannot be formatted into a date.");
        }
    }
}
