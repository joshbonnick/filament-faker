<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use FilamentFaker\Contracts\FakerProvider;
use Illuminate\Support\Str;

class DefaultFakers implements FakerProvider
{
    public function withOptions(Field $component): mixed
    {
        if (! method_exists($component, 'getOptions')
            || empty($options = $component->getOptions())
        ) {
            return $this->defaultCallback($component);
        }

        if ($component instanceof CheckboxList
            || (method_exists($component, 'isMultiple') && $component->isMultiple())) {
            return fake()->randomElements(array_keys($options));
        }

        return fake()->randomElement(array_keys($options));
    }

    /**
     * @return array<int, string|int|float>
     */
    public function withSuggestions(TagsInput $component): array
    {
        if (empty($suggestions = $component->getSuggestions())) {
            return fake()->rgbColorAsArray();
        }

        return fake()->randomElements(
            array: $suggestions,
            count: ($numOfSuggestions = count($suggestions)) > 1
                ? fake()->numberBetween(1, $numOfSuggestions - 1)
                : 1
        );
    }

    public function date(): string
    {
        return now()->toFormattedDateString();
    }

    public function file(FileUpload $upload): string
    {
        if (in_array('image/*', $upload->getAcceptedFileTypes() ?? [])) {
            return 'https://placehold.co/600x400.png';
        }

        return Str::random(8).'.txt';
    }

    /**
     * @return string[]
     */
    public function keyValue(KeyValue $component): array
    {
        return ['key' => 'value'];
    }

    public function color(ColorPicker $color): string
    {
        return match ($color->getFormat()) {
            'hsl' => Str::wrap(fake()->hslColor(), 'hsl(', ')'),
            'rgb' => fake()->rgbCssColor(),
            'rgba' => fake()->rgbaCssColor(),
            default => fake()->safeHexColor(),
        };
    }

    public function html(): string
    {
        return Str::wrap(fake()->sentence(), '<p>', '</p>');
    }

    public function checkbox(): bool
    {
        return fake()->boolean();
    }

    public function defaultCallback(Field $component): string
    {
        return fake()->sentence();
    }
}
