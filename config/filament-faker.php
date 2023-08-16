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
        | Override faking methods for a component or add custom components.
        |
        */
        // Example override built in component
        //
        // ColorPicker::class => fn (ColorPicker $component): string => '#f7f7f7',

        // Example plugin component faking...
        //
        // SpatieMediaLibraryFileUpload::class => fn (SpatieMediaLibraryFileUpload $component) => 'https://placehold.co/600x400.png',
        // IconPicker::class => fn (IconPicker $component) => 'fa-light fa-user',
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
