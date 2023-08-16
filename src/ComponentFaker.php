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
use FilamentFaker\Concerns\GeneratesFakesFromComponentName;
use FilamentFaker\Concerns\InteractsWithFakeConfig;
use FilamentFaker\Contracts\FakesComponents;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ComponentFaker implements FakesComponents
{
    use GeneratesFakesFromComponentName;
    use InteractsWithFakeConfig;

    protected Field $component;

    public function __construct()
    {
        $this->setUpConfig();
    }

    public function fake(Field $component): mixed
    {
        $this->component = $component;

        return $this->fakeComponentContent($this->component);
    }

    protected function fakeComponentContent(Field $component): mixed
    {
        if (method_exists($component, 'mutateFake')) {
            return $component->mutateFake($component);
        }

        if ($this->shouldFakeUsingComponentName($component) && ! method_exists($component, 'getOptions')) {
            try {
                $content = $this->fakeUsingComponentName($component);

                if (! is_null($content)) {
                    return $content;
                }
            } catch (InvalidArgumentException $e) {
            }
        }

        return $this->getCallback($component)($component);

        if (Arr::has($this->fakesConfig, $component::class)) {
            return $this->fakesConfig[$component::class]($component);
        }

        return $this->fakesConfig['default']($component);
    }

    /**
* @return Closure(Field $component): mixed
     */
    protected function getCallback(Field $component): Closure{
        if (Arr::has($this->fakesConfig, $component::class)) {
            return $this->fakesConfig[$component::class];
        }

        return match($component::class){
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
            default => fn () => fake()->sentence(),
        };
    }
}
