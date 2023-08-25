<?php

use Filament\Forms\Components\TextInput;
use FilamentFaker\Contracts\Fakers\FakesForms;
use FilamentFaker\Tests\Feature\Fakers\Blocks\Fixtures\MockBlock;
use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\MultipleForms;
use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\PostResource;

beforeEach(function () {
    mockComponentDecorator();
});

it('can fake forms', function () {
    expect($fake = PostResource::fake())
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

it('can fake multiple forms', function () {
    expect(MultipleForms::fake('editPostForm'))
        ->toBeArray()
        ->toHaveKeys(['title', 'content'])
        ->and(MultipleForms::fake('createCommentForm'))
        ->toBeArray()
        ->toHaveKeys(['name', 'email', 'content']);
});

it('can fake multiple forms using resource faker', function () {
    expect(MultipleForms::faker()->withForm('editPostForm')->fake())
        ->toBeArray()
        ->toHaveKeys(['title', 'content'])
        ->and(MultipleForms::faker()->withForm('createCommentForm')->fake())
        ->toBeArray()
        ->toHaveKeys(['name', 'email', 'content']);
});

it('returns hidden fields when withHidden used', function () {
    $form = PostResource::faker()->getForm()->schema([
        TextInput::make('hidden_field')->hidden(),
    ]);

    expect($form->faker()->withoutHidden()->fake())
        ->not
        ->toHaveKeys([
            'hidden_field',
        ])
        ->and($form->faker()->fake())
        ->toHaveKeys([
            'hidden_field',
        ]);
});

test('faker returns an instance of FakesForms', function () {
    expect(PostResource::faker()->getForm()->faker())
        ->toBeInstanceOf(FakesForms::class);
});
