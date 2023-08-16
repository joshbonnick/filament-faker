<?php

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;

return [
    /*
    |
    | If Faker has a method that matches the component name, the result of that
    | method will be returned as the components faked value.
    |
    | You can disable this feature for all Blocks or disable it per block.
    |
    */
    'use_component_names_for_fake' => true,

    'fakes' => [
        /*
        |--------------------------------------------------------------------------
        | Filament Config: Fakes
        |--------------------------------------------------------------------------
        |
        | This configuration file defines the behavior for generating fake data for
        | Filament Forms components. Key-value pairs where each key represents a
        | specific Filament Forms component, and the associated value is a
        | closure that generates fake data for that component.
        |
        | Feel free to customize these fake data generation methods according to your
        | application's needs to simulate data in Filament Forms.
        |
        */
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

        // This entry defines default fake data generation.
        'default' => fn (Component $component): string => fake()->sentence(),
    ],

    'slow_faker_methods' => [

        /*
        |--------------------------------------------------------------------------
        | Slow Faker Methods
        |--------------------------------------------------------------------------
        |
        | This section allows you to specify an array of Faker methods that are slow
        | and will not be used within this package.
        |
        | These methods can impact the performance of your test suite, so excluding
        | them can help decrease run time.
        |
        | Method names should be camel case.
        |
        */

        'image',
        'imageUrl',
        'file',
        'fileUrl',
        'dateTimeBetween',
        'randomHtml',
    ],
];
