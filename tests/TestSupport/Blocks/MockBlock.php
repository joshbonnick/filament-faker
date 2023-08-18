<?php

namespace FilamentFaker\Tests\TestSupport\Blocks;

use Closure;
use Filament\Forms\Components;

class MockBlock extends Components\Builder\Block
{
    public function mutateFake(Components\Field $component): ?Closure
    {
        return match ($component->getName()) {
            'phone_number' => fn () => '::phone::',
            'email_field' => fn () => 'dev@example.com',
            default => null,
        };
    }

    public static function make(string $name = null): static
    {
        return parent::make($name ?? static::class)
            ->label('Rich Editor')
            ->icon('heroicon-m-bars-3-bottom-left')
            ->schema([
                Components\Select::make('color')
                    ->options($options = [
                        '#f00' => 'Red',
                        '#0f0' => 'Green',
                        '#00f' => 'Blue',
                    ]),
                Components\RichEditor::make('content'),
                Components\Radio::make('radio')->options($options),
                Components\TagsInput::make('tags'),
                Components\TagsInput::make('suggested_tags')->suggestions(['foo']),
                Components\Checkbox::make('checkbox'),
                Components\CheckboxList::make('checkbox_list')->options($options),
                Components\Toggle::make('toggle'),
                Components\DateTimePicker::make('datetime'),
                Components\FileUpload::make('some_image')->image(),
                Components\FileUpload::make('some_file'),
                Components\KeyValue::make('key_value'),
                Components\ColorPicker::make('color_rgb')->rgb(),
                Components\ColorPicker::make('color_rgba')->rgba(),
                Components\ColorPicker::make('color_hsl')->hsl(),
                Components\ColorPicker::make('color_hex')->hex(),
                Components\TextInput::make('email_field'),
                Components\TextInput::make('safe_email'),
                Components\TextInput::make('phone_number'),
                Components\TextInput::make('title'),
                Components\Select::make('company')->options([
                    'foo' => 'bar',
                    'bar' => 'foo',
                ]),
            ]);
    }
}
