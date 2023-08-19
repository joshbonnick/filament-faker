<?php

namespace FilamentFaker\Tests\TestSupport\Resources;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;

class MutatedResource extends Resource
{
    public function mutateFake(Field $component): string
    {
        return '::mutated-in-resource::';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('safe_email'),
            TextInput::make('hidden_field')->hidden(),
            TextInput::make('title'),
            Builder::make('content')->blocks([
                MockBlock::make(),
            ]),
        ]);
    }
}
