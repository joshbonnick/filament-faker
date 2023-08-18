<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use BadMethodCallException;
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
use FilamentFaker\Contracts\DataGenerator;
use FilamentFaker\Contracts\FakesComponents;
use FilamentFaker\Contracts\RealTimeFactory;
use FilamentFaker\Support\ComponentDecorator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

use function FilamentFaker\callOrReturn;

class ComponentFaker extends FilamentFaker implements FakesComponents
{
    public function __construct(
        protected readonly DataGenerator $faker,
        protected readonly RealTimeFactory $realTimeFactory,
        protected readonly ComponentDecorator $component,
        Field $field,
    ) {
        $this->component->setUp($field);
    }

    public function fake(): mixed
    {
        if ($this->mutateCallback instanceof Closure) {
            return ($this->mutateCallback)($this->component());
        }

        if (! is_null($mutateCallbackResponse = $this->attemptToCallMutationMacro())) {
            return $mutateCallbackResponse;
        }

        if ($this->factoryDefinitionExists()) {
            return $this->getModelAttributes()[$this->component()->getName()];
        }

        if ($this->getShouldFakeUsingComponentName()) {
            $data = $this->realTimeFactory->fakeFromName($this->component()->getName());
        }

        return $this->component
            ->setState($data ?? callOrReturn($this->generateComponentData(), $this->component()))
            ->format();
    }

    protected function attemptToCallMutationMacro(): mixed
    {
        try {
            return callOrReturn($this->component()->mutateFake($this->component()), $this->component()); // @phpstan-ignore-line
        } catch (BadMethodCallException $e) {
        }

        return null;
    }

    protected function generateComponentData(): mixed
    {
        if ($this->component->hasOverride()) {
            return callOrReturn($this->config()[$this->component()::class], $this->component());
        }

        return match ($this->component()::class) {
            CheckboxList::class,
            Radio::class,
            Select::class => $this->faker->withOptions($this->component()),
            Checkbox::class,
            Toggle::class => $this->faker->checkbox(),
            TagsInput::class => $this->faker->withSuggestions($this->component()),
            DatePicker::class,
            DateTimePicker::class => $this->faker->date(),
            FileUpload::class => $this->faker->file($this->component()),
            KeyValue::class => $this->faker->keyValue($this->component()),
            ColorPicker::class => $this->faker->color($this->component()),
            RichEditor::class => $this->faker->html(),
            default => $this->faker->defaultCallback($this->component()),
        };
    }

    protected function component(): Field
    {
        return $this->component->component();
    }

    /**
     * @return class-string<Model>|string|null
     *
     * @throws InvalidArgumentException
     */
    protected function resolveModel(): ?string
    {
        return $this->component()->getModel();
    }

    protected function factoryDefinitionExists(): bool
    {
        return Arr::has($this->getModelAttributes(), $this->component()->getName());
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
            && ! $this->component->hasOptions();
    }
}
