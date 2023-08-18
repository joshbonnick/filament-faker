<?php

namespace FilamentFaker\Tests\TestSupport\Resources;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Models\Post;
use FilamentFaker\Tests\TestSupport\Models\WithoutFactory;

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
