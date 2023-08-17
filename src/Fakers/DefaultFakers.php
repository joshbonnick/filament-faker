<?php

declare(strict_types=1);

namespace FilamentFaker\Fakers;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Concerns\HasOptions;
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
        if (! in_array(HasOptions::class, class_uses_recursive($component))) {
            return $this->defaultCallback($component);
        }

        if(empty($options = $component->getOptions())){
            return fake()->randomElement(array_keys($options));
        }

        if ($component instanceof CheckboxList
            || (method_exists($component, 'isMultiple') && $component->isMultiple())) {
            return fake()->randomElements(array_keys($options));
        }

        return fake()->randomElement(array_keys($options));
    }

    public function withSuggestions(TagsInput $component): array
    {
        if (empty($suggestions = $component->getSuggestions())) {
            return fake()->rgbColorAsArray();
        }

        return fake()->randomElements(
            array: $suggestions,
            count: count($suggestions) > 1
                ? fake()->numberBetween(1, count($suggestions) - 1)
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

        return str(Str::random(8))->append('.txt')->toString();
    }

    public function keyValue(KeyValue $component): array
    {
        return ['key' => 'value'];
    }

    public function color(ColorPicker $color): string
    {
        return match ($color->getFormat()) {
            'hsl' => str(fake()->hslColor())->wrap('hsl(', ')')->toString(),
            'rgb' => fake()->rgbCssColor(),
            'rgba' => fake()->rgbaCssColor(),
            default => fake()->safeHexColor(),
        };
    }

    public function html(): string
    {
        return str(fake()->sentence())->wrap('<p>', '</p>')->toString();
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
