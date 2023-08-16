<?php

namespace FilamentFaker\Tests\TestSupport\Resources;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlockWithoutFakingFromNames;

class PostResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title'),
            TextInput::make('company'),
            ColorPicker::make('brand_color')->hsl(),
            Builder::make('content')->blocks([
                MockBlock::make(),
                MockBlockWithoutFakingFromNames::make(),
            ]),
        ]);
    }
}
