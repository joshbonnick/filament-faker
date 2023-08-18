<?php

return [
    /**
     * Custom components added via plugins or your own code base should be added here
     * with their default faker callback.
     */
    'fakes' => [
        // ColorPicker::class => fn (ColorPicker $component): string => '#f7f7f7',

        // SpatieMediaLibraryFileUpload::class => fn (SpatieMediaLibraryFileUpload $component) => 'https://placehold.co/600x400.png',
        // IconPicker::class => fn (IconPicker $component) => 'fa-light fa-user',
    ],

    /**
     * If Faker has a method that matches the component name, the result of that
     * method will be returned as the components faked value.
     *
     * You can disable this feature for the package or disable it per faker instance.
     */
    'use_component_names_for_fake' => true,

    /**
     * This section allows you to specify an array of Faker methods that should not be
     * run and will not be used within this package.
     *
     * The default methods listed here can impact the performance of your test suite, so excluding
     * them can help decrease run time.
     */
    'excluded_faker_methods' => [
        'image',
        'imageUrl',
        'file',
        'fileUrl',
        'dateTimeBetween',
        'randomHtml',
    ],
];
