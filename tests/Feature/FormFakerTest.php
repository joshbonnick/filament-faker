<?php

use FilamentFaker\Tests\TestSupport\Blocks\MockBlock;
use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('can fake forms', function () {
    expect($fake = PostResource::fakeForm())
        ->toBeArray()
        ->toHaveKeys([
            'title', 'company', 'brand_color', 'content',
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
        ->toEqual(MockBlock::class);
});
