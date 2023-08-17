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
use FilamentFaker\Tests\TestSupport\Blocks\MockBlockWithoutFakingFromNames;
use FilamentFaker\Tests\TestSupport\Models\Post;

class PostResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('safe_email'),
            TextInput::make('hidden_field')->hidden(),
            TextInput::make('title'),
            TextInput::make('company'),
            ColorPicker::make('brand_color')->hsl(),

            Select::make('parent_id')
                ->relationship('parent', 'title')
                ->label('Primary Category')
                ->searchable()
                ->options(fn () => Post::query()->select(['id', 'title'])->get()->pluck('title', 'id'))
                ->required(),

            Section::make()->schema([
                Builder::make('section_content')->blocks([
                    Builder\Block::make('Some Block')->schema([
                        TextInput::make('blockfoo'),
                    ]),
                ]),
            ]),

            Builder::make('content')->blocks([
                MockBlock::make(),
                MockBlockWithoutFakingFromNames::make(),
            ]),

            Section::make()->schema([
                TextInput::make('section_foo'),
            ]),

            Group::make()->schema([
                TextInput::make('foo'),

                Section::make()->schema([
                    TextInput::make('bar'),
                ]),
            ]),

            Wizard::make([
                Wizard\Step::make('Order')
                    ->schema([
                        TextInput::make('wiz_foo'),
                    ]),
                Wizard\Step::make('Delivery')
                    ->schema([
                        TextInput::make('wiz_bar'),
                    ]),
            ]),

            Tabs::make()->schema([
                TextInput::make('tab_foo'),
                Group::make()->schema([
                    TextInput::make('tab_bar'),
                ]),
                Section::make()->schema([
                    TextInput::make('tab_foobar'),
                ]),
            ]),
            Fieldset::make()
                ->schema([
                    TextInput::make('fieldset_foo'),
                    Group::make()->schema([
                        TextInput::make('fieldset_bar'),
                    ]),
                    Section::make()->schema([
                        TextInput::make('fieldset_foobar'),
                    ]),
                ]),

            Grid::make([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
                'xl' => 6,
                '2xl' => 8,
            ])
                ->schema([
                    TextInput::make('grid_foo'),
                    Group::make()->schema([
                        TextInput::make('grid_bar'),
                    ]),
                    Section::make()->schema([
                        TextInput::make('grid_foobar'),
                    ]),
                ]),
        ]);
    }
}
