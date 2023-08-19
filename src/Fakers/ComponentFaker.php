<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Component;
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
use FilamentFaker\Contracts\Fakers\FakesComponents;
use FilamentFaker\Contracts\Support\DataGenerator;
use FilamentFaker\Contracts\Support\RealTimeFactory;
use FilamentFaker\Support\ComponentDecorator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use ReflectionException;

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
        if (is_callable($this->mutateCallback)) {
            return $this->resolveOrReturn($this->mutateCallback);
        }

        if (! is_null($mutateCallbackResponse = $this->attemptToCallMutationMacro())) {
            return $mutateCallbackResponse;
        }

        if ($this->factoryDefinitionExists()) {
            return $this->getModelAttributes()[$this->component->getName()];
        }

        if ($this->getShouldFakeUsingComponentName()) {
            $data = $this->realTimeFactory->fakeFromName($this->component->getName());
        }

        return $this->component
            ->setState($data ?? $this->resolveOrReturn($this->generateComponentData()))
            ->format();
    }

    protected function attemptToCallMutationMacro(): mixed
    {
        try {
            return $this->resolveOrReturn([$this->component(), 'mutateFake']);
        } catch (ReflectionException $e) {
            throw_unless(str_contains($e->getMessage(), 'mutateFake() does not exist'));
        }

        return null;
    }

    protected function generateComponentData(): mixed
    {
        if ($this->component->hasOverride()) {
            return $this->resolveOrReturn($this->config()[$this->component()::class]);
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
        return $this->component->getField();
    }

    /**
     * @return array<class-string|string, object>
     */
    protected function injectionParameters(): array
    {
        return [
            Field::class => $this->component(),
            Component::class => $this->component(),
            $this->component()::class => $this->component(),
        ];
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
