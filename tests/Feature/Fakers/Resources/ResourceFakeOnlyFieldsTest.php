<?php

use FilamentFaker\Tests\TestSupport\Resources\PostResource;

it('returns only fields in only fields array', function () {
    $resourceFaker = PostResource::faker()->onlyFields('safe_email', 'title');

    expect(array_keys($resourceFaker->fake()))->toEqual(['safe_email', 'title']);
});
