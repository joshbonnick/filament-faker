<?php

namespace FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use FilamentFaker\Tests\Feature\Fixtures\Models\Post;

class ProductResource extends Resource
{
    protected static ?string $model = Post::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('safe_email'),
            TextInput::make('title'),
        ]);
    }
}
