<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
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
use FilamentFaker\Concerns\GeneratesFakes;
use FilamentFaker\Concerns\InteractsWithFactories;
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Contracts\FakerProvider;
use FilamentFaker\Contracts\FakesComponents;
use Illuminate\Support\Arr;
use ReflectionException;
use ReflectionProperty;
use Throwable;

class ComponentFaker extends GeneratesFakes implements FakesComponents
{
    use InteractsWithFilamentContainer;
    use InteractsWithFactories;

    protected Field $component;

    public function __construct(
        protected readonly FakerProvider $faker,
        Field $component,
    ) {
        parent::__construct();

        $this->component = tap($component)->container($this->container());
    }

    public function fake(): mixed
    {
        return $this->fakeComponentContent();
    }

    protected function fakeComponentContent(): mixed
    {
        if (method_exists($this->component, 'mutateFake')) {
            return $this->component->mutateFake($this->component);
        }

        if (Arr::has($model = $this->getModelAttributes(), $componentName = $this->component->getName())) {
            return $model[$componentName];
        }

        if ($this->shouldFakeUsingComponentName($this->component)
            && ! method_exists($this->component, 'getOptions')
        ) {
            $content = $this->fakeUsingComponentName($this->component);
        }

        $content ??= ($faked = $this->getFake()) instanceof Closure
            ? $faked($this->component)
            : $faked;

        return $this->format($content);
    }

    protected function format(mixed $fakedContent): mixed
    {
        try {
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

    protected function getFake(): mixed
    {
        if (Arr::has($this->fakesConfig, $this->component::class)) {
            return $this->fakesConfig[$this->component::class];
        }

        return match ($this->component::class) {
            CheckboxList::class,
            Radio::class,
            Select::class => $this->faker->withOptions($this->component),
            Checkbox::class,
            Toggle::class => $this->faker->checkbox(),
            TagsInput::class => $this->faker->withSuggestions($this->component),
            DateTimePicker::class => $this->faker->date(),
            FileUpload::class => $this->faker->file($this->component),
            KeyValue::class => $this->faker->keyValue($this->component),
            ColorPicker::class => $this->faker->color($this->component),
            RichEditor::class => $this->faker->html(),
            default => $this->faker->defaultCallback($this->component),
        };
    }
}
