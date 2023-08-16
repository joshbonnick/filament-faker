<?php

declare(strict_types=1);

namespace FilamentFaker;

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
use FilamentFaker\Concerns\InteractsWithFilamentContainer;
use FilamentFaker\Contracts\FakesComponents;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionProperty;
use Throwable;

class ComponentFaker extends GeneratesFakes implements FakesComponents
{
    use InteractsWithFilamentContainer;

    protected Field $component;

    public function fake(Field $component): mixed
    {
        $this->component = tap($component)->container($this->container());

        return $this->fakeComponentContent();
    }

    protected function fakeComponentContent(): mixed
    {
        if (method_exists($this->component, 'mutateFake')) {
            return $this->component->mutateFake($this->component);
        }

        if ($this->shouldFakeUsingComponentName($this->component) && ! method_exists($this->component, 'getOptions')) {
            $content = $this->fakeUsingComponentName($this->component);
        }

        return $this->format($content ?? $this->getCallback()($this->component));
    }

    /**
     * @throws ReflectionException
     */
    protected function format(mixed $faked): mixed
    {
        $formatter = tap(new ReflectionProperty($this->component, 'afterStateHydrated'))->setAccessible(true);

        try {
            $this->component->state(fn (Set $set) => $set($this->component->getName(), $faked));

            if (is_null($callback = $formatter->getValue($this->component))) {
                return $faked;
            }

            if ($callback instanceof Closure) {
                return $callback($this->component, $faked)?->getState() ?? $faked;
            }
        } catch (Throwable $e){
        }

        return $faked;
    }

    protected function getCallback(): Closure
    {
        if (Arr::has($this->fakesConfig, $this->component::class)) {
            return $this->fakesConfig[$this->component::class];
        }

        return match ($this->component::class) {
            Select::class => fn (Select $component): mixed => fake()->randomElement(array_keys($component->getOptions())),

            Radio::class => fn (Radio $component): mixed => fake()->randomElement(array_keys($component->getOptions())),

            TagsInput::class => function (TagsInput $component): array {
                if (empty($suggestions = $component->getSuggestions())) {
                    return fake()->rgbColorAsArray();
                }

                return fake()->randomElements(
                    array: $suggestions,
                    count: count($suggestions) > 1
                        ? fake()->numberBetween(1, count($suggestions) - 1)
                        : 1
                );
            },

            Checkbox::class => fn (Checkbox $component): bool => fake()->boolean(),

            CheckboxList::class => fn (CheckboxList $component): array => fake()->randomElements(
                array: array_keys($options = $component->getOptions()),
                count: fake()->numberBetween(1, count($options))
            ),

            Toggle::class => fn (Toggle $component): bool => fake()->boolean(),

            DateTimePicker::class => fn (DateTimePicker $component): string => now()->toFormattedDateString(),

            FileUpload::class => function (FileUpload $component): string {
                if (in_array('image/*', $component->getAcceptedFileTypes() ?? [])) {
                    return 'https://placehold.co/600x400.png';
                }

                return str(Str::random(8))->append('.txt')->toString();
            },

            KeyValue::class => fn (KeyValue $component): array => ['key' => 'value'],

            ColorPicker::class => fn (ColorPicker $component): string => match ($component->getFormat()) {
                'hsl' => str(fake()->hslColor())->wrap('hsl(', ')')->toString(),
                'rgb' => fake()->rgbCssColor(),
                'rgba' => fake()->rgbaCssColor(),
                default => fake()->safeHexColor(),
            },

            RichEditor::class => fn (RichEditor $component): string => str(fake()->sentence())->wrap('<p>', '</p>')->toString(),

            default => fn (Field $component) => fake()->sentence(),
        };
    }
}
