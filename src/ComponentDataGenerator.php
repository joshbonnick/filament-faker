<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;
use FilamentFaker\Contracts\Support\DataGenerator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ComponentDataGenerator implements DataGenerator
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
    public function withSuggestions(Field $component): array
    {
        if (! method_exists($component, 'getSuggestions')) {
            throw new InvalidArgumentException("{$component->getName()} does not have suggestions.");
        }

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

    public function file(Field $upload): string
    {
        if (method_exists($upload, 'getAcceptedFileTypes')
            && in_array('image/*', $upload->getAcceptedFileTypes() ?? [])) {
            return 'https://placehold.co/600x400.png';
        }

        return Str::random(8).'.txt';
    }

    /**
     * @return string[]
     */
    public function keyValue(Field $component): array
    {
        return ['key' => 'value'];
    }

    public function color(Field $color): string
    {
        if (! method_exists($color, 'getFormat')) {
            return fake()->safeHexColor();
        }

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
