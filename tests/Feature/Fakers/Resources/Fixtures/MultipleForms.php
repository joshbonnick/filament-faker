<?php

namespace FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class MultipleForms extends Resource
{
    protected function getForms(): array
    {
        return [
            'editPostForm',
            'createCommentForm',
        ];
    }

    public function editPostForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),
                MarkdownEditor::make('content'),
            ])
            ->statePath('postData');
    }

    public function createCommentForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                MarkdownEditor::make('content')
                    ->required(),
            ])
            ->statePath('commentData');
    }
}
