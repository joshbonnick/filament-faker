<?php

namespace FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Tests\Feature\Fakers\Blocks\Fixtures\MockBlock;

class MutatedResource extends Resource
{
    public function mutateFake(Field $component): ?string
    {
        return $component->getName() === 'safe_email' ? '::mutated-in-resource::' : null;
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
