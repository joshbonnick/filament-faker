<?php

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\Fakers\FakesResources;
use FilamentFaker\Tests\Feature\Fakers\Blocks\Fixtures\MockBlock;
use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\InjectedResource;
use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\PostResource;
use Illuminate\Contracts\Container\BindingResolutionException;

beforeEach(function () {
    mockComponentDecorator();
});

it('can fake resources', function () {
    expect($fake = PostResource::faker()->fake())
        ->toBeArray()
        ->toHaveKeys([
            'title', 'company', 'brand_color', 'content', 'foo', 'bar',
            'wiz_foo', 'wiz_bar', 'tab_foo', 'tab_foobar', 'tab_bar',
            'fieldset_foo', 'fieldset_foobar', 'fieldset_bar',
            'grid_foo', 'grid_foobar', 'grid_bar', 'section_foo',
            'section_content',
        ])
        ->and($fake['title'])
        ->toBeString()
        ->and($fake['company'])
        ->toBeString()
        ->and($fake['brand_color'])
        ->toBeString()
        ->toStartWith('hsl(')
        ->and($fake['content'])
        ->toBeArray()
        ->toHaveCount(1)
        ->and($fake['content'][0]['type'])
        ->toEqual(MockBlock::class)
        ->and($fake['foo'])
        ->toBeString()
        ->and($fake['bar'])
        ->toBeString();
});

it('accepts an instance of a form', function () {
    $form = PostResource::faker()->getForm()->schema([
        TextInput::make('foo'),
        RichEditor::make('bar'),
        ColorPicker::make('baz'),
    ]);

    expect(PostResource::faker()->withForm($form)->fake())
        ->toBeArray()
        ->toHaveKeys([
            'foo', 'bar', 'baz',
        ]);
});

test('faker returns an instance of FakesResources', function () {
    expect(PostResource::faker())
        ->toBeInstanceOf(FakesResources::class);
});

test('form fake is returned if cannot resolve the resource', function () {
    expect(fn () => resolve(InjectedResource::class))
        ->toThrow(BindingResolutionException::class)
        ->and(InjectedResource::fake())
        ->toBeArray();
});
