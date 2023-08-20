<?php

declare(strict_types=1);

namespace FilamentFaker;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use FilamentFaker\Contracts\Support\DataGenerator;
use FilamentFaker\Decorators\ComponentDecorator;
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

    public function generate(): mixed
    {
        return match ($this->component->getField()::class) {
            CheckboxList::class,
            Radio::class,
            Select::class => $this->withOptions(),
            Checkbox::class,
            Toggle::class => $this->checkbox(),
            TagsInput::class => $this->withSuggestions(),
            DatePicker::class,
            DateTimePicker::class => $this->date(),
            FileUpload::class => $this->file(),
            KeyValue::class => $this->keyValue(),
            ColorPicker::class => $this->color(),
            RichEditor::class => $this->html(),
            default => $this->defaultCallback(),
        };
    }

    protected function withOptions(): mixed
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
