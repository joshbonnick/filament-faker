<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components;
use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Contracts\Support\DataGenerator;
use FilamentFaker\Contracts\Support\RealTimeFactory;
use FilamentFaker\Exceptions\InvalidComponentOptionsException;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ComponentDataGenerator implements DataGenerator
{
    protected ComponentDecorator $component;

    public function uses(ComponentDecorator $component): static
    {
        return tap($this, function () use ($component): void {
            $this->component = $component;
        });
    }

    public function realTime(): RealTimeFactory
    {
        return app(RealTimeFactory::class);
    }

    /**
     * @throws InvalidComponentOptionsException
     */
    public function generate(): mixed
    {
        return match ($this->component->getField()::class) {
            Components\CheckboxList::class,
            Components\Radio::class,
            Components\Select::class => $this->withOptions(),
            Components\Checkbox::class,
            Components\Toggle::class => $this->checkbox(),
            Components\TagsInput::class => $this->withSuggestions(),
            Components\DatePicker::class,
            Components\DateTimePicker::class => $this->date(),
            Components\FileUpload::class => $this->file(),
            Components\KeyValue::class => $this->keyValue(),
            Components\ColorPicker::class => $this->color(),
            Components\RichEditor::class => $this->html(),
            default => $this->defaultCallback(),
        };
    }

    /**
     * @throws InvalidComponentOptionsException
     */
    protected function withOptions(): mixed
    {
        if (! $this->component->hasOptions()) {
            return $this->defaultCallback();
        }

        if (! empty($options = array_keys($this->component->getOptions()))) {
            if ($this->component->is_a(Components\CheckboxList::class) || $this->component->isMultiple()) {
                return fake()->randomElements($options);
            }

            return fake()->randomElement($options);
        }

        if (! $this->component->isSearchable()) {
            throw_if(
                $this->component->isRequired(),
                InvalidComponentOptionsException::class,
                "{$this->component->getName()} is required. Options array is empty."
            );

            return null;
        }

        if (empty($searchResults = array_keys($this->component->getSearch()))) {
            throw_if(
                $this->component->isRequired(),
                InvalidComponentOptionsException::class,
                "{$this->component->getName()} is required. Options and search array is empty."
            );

            return null;
        }

        return $this->component->isMultiple()
            ? fake()->randomElements($searchResults)
            : fake()->randomElement($searchResults);
    }

    /**
     * @return array<int, string|int|float>
     */
    protected function withSuggestions(): array
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

    protected function date(): string
    {
        return now()->toFormattedDateString();
    }

    protected function file(): string
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
    protected function keyValue(): array
    {
        return ['key' => 'value'];
    }

    protected function color(): string
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

    protected function html(): string
    {
        return Str::wrap(fake()->sentence(), '<p>', '</p>');
    }

    protected function checkbox(): bool
    {
        return fake()->boolean();
    }

    protected function defaultCallback(): string
    {
        return fake()->sentence();
    }
}
