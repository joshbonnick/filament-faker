<?php

use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Resources\MultipleForms;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('can fake forms', function () {
    expect($fake = PostResource::fakeForm())
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
        ->toHaveCount(2)
        ->and($fake['content'][0]['type'])
        ->toEqual(MockBlock::class)
        ->and($fake['foo'])
        ->toBeString()
        ->and($fake['bar'])
        ->toBeString();
});

it('can fake multiple forms', function () {
    expect(MultipleForms::fakeForm('editPostForm'))
        ->toBeArray()
        ->toHaveKeys(['title', 'content'])
        ->and(MultipleForms::fakeForm('createCommentForm'))
        ->toBeArray()
        ->toHaveKeys(['name', 'email', 'content']);
});

it('returns hidden fields when withHidden used', function () {
    expect($fake = PostResource::fakeForm())
        ->toBeArray()
        ->toHaveKeys([
            'hidden_field',
        ]);
});
