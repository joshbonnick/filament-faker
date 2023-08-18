<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use BadMethodCallException;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use FilamentFaker\Contracts\DataGenerator;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\RealTimeFactory;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use ReflectionException;
use ReflectionProperty;
use Throwable;

use function FilamentFaker\callOrReturn;

class ComponentFaker extends FilamentFaker implements FakesComponents
{
    protected Field $component;

    public function __construct(
        protected readonly DataGenerator $faker,
        protected readonly RealTimeFactory $realTimeFactory,
        Field $component,
    ) {
        $this->component = $this->setUpComponent($component);
    }

    public function fake(): mixed
    {
        if ($this->mutateCallback instanceof Closure) {
            return ($this->mutateCallback)($this->component);
        }

        if (! is_null($mutateCallbackResponse = $this->attemptToCallMutationMacro())) {
            return $mutateCallbackResponse;
        }

        if ($this->factoryDefinitionExists()) {
            return $this->getModelAttributes()[$this->component->getName()];
        }

        if ($this->getShouldFakeUsingComponentName()) {
            $content = $this->realTimeFactory->fakeFromName($this->component->getName());
        }

        return $this->format($content ?? callOrReturn($this->generateComponentData(), $this->component));
    }

    protected function format(mixed $fakedContent): mixed
    {
        try {
            if (is_a($this->component, DateTimePicker::class)) {
                return $this->formatDate($fakedContent);
            }

            $afterStateHydrated = tap(new ReflectionProperty($this->component, 'afterStateHydrated'))->setAccessible(true);

            $this->component->state(fn (Set $set) => $set($this->component->getName(), $fakedContent));

            if (is_null($callback = $afterStateHydrated->getValue($this->component))) {
                return $fakedContent;
            }

            if ($callback instanceof Closure) {
                return $callback($this->component, $fakedContent)?->getState() ?? $fakedContent;
            }
        } catch (ReflectionException $e) {
            report($e);
        } catch (Throwable $e) {
        }

        return $fakedContent;
    }

    protected function formatDate(string $fakedContent): string
    {
        if (! is_a($this->component, DateTimePicker::class)) {
            throw new InvalidArgumentException("{$this->component->getName()} cannot be formatted into a date.");
        }

        return Carbon::parse($fakedContent)->format($this->component->getFormat());
    }

    protected function attemptToCallMutationMacro(): mixed
    {
        try {
            return callOrReturn($this->component->mutateFake($this->component), $this->component); // @phpstan-ignore-line
        } catch (BadMethodCallException $e) {
        }

        return null;
    }

    protected function generateComponentData(): mixed
    {
        if (Arr::has($config = $this->config(), $class = $this->component::class)) {
            return $config[$class];
        }

        return match ($this->component::class) {
            CheckboxList::class,
            Radio::class,
            Select::class => $this->faker->withOptions($this->component),
            Checkbox::class,
            Toggle::class => $this->faker->checkbox(),
            TagsInput::class => $this->faker->withSuggestions($this->component),
            DatePicker::class,
            DateTimePicker::class => $this->faker->date(),
            FileUpload::class => $this->faker->file($this->component),
            KeyValue::class => $this->faker->keyValue($this->component),
            ColorPicker::class => $this->faker->color($this->component),
            RichEditor::class => $this->faker->html(),
            default => $this->faker->defaultCallback($this->component),
        };
    }

    protected function factoryDefinitionExists(): bool
    {
        return Arr::has($this->getModelAttributes(), $this->component->getName());
    }

    /**
     * Resolve whether Faker should be using the components name for generating data.
     */
    protected function getShouldFakeUsingComponentName(): bool
    {
        if ($this->shouldFakeUsingComponentName === false) {
            return false;
        }

        return config('filament-faker.fake_using_component_name', true)
            && ! method_exists($this->component, 'getOptions');
    }
}
