<?php

namespace FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Tests\Feature\Fakers\Blocks\Fixtures\MockBlock;
use FilamentFaker\Tests\Feature\Fixtures\Models\WithoutFactory;

class WithoutFactoryResource extends Resource
{
    protected static ?string $model = WithoutFactory::class;

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
