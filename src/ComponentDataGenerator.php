<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\CheckboxList;
use FilamentFaker\Contracts\Support\DataGenerator;
use FilamentFaker\Support\ComponentDecorator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ComponentDataGenerator implements DataGenerator
{
    protected ComponentDecorator $component;

    public function using(ComponentDecorator $component): static
    {
        return tap($this, function () use ($component): void {
            $this->component = $component;
        });
    }

    public function withOptions(): mixed
    {
        if (! $this->component->hasOptions()) {
            return $this->defaultCallback();
        }

        if ($this->component->is_a(CheckboxList::class) || $this->component->isMultiple()) {
            return fake()->randomElements(array_keys($this->component->getOptions()));
        }

        return fake()->randomElement(array_keys($this->component->getOptions()));
    }

    /**
     * @return array<int, string|int|float>
     */
    public function withSuggestions(): array
    {
        if ($this->component->missingMethod('getSuggestions')) {
            throw new InvalidArgumentException("{$this->component->getName()} does not have suggestions.");
        }

        if (empty($suggestions = $this->component->getSuggestions())) {
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

    public function file(): string
    {
        if ($this->component->hasMethod('getAcceptedFileTypes')
            && in_array('image/*', $this->component->getAcceptedFileTypes() ?? [])) {
            return 'https://placehold.co/600x400.png';
        }

        return Str::random(8).'.txt';
    }

    /**
     * @return string[]
     */
    public function keyValue(): array
    {
        return ['key' => 'value'];
    }

    public function color(): string
    {
        if ($this->component->missingMethod('getFormat')) {
            return fake()->safeHexColor();
        }

        return match ($this->component->getFormat()) {
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

    public function defaultCallback(): string
    {
        return fake()->sentence();
    }
}
