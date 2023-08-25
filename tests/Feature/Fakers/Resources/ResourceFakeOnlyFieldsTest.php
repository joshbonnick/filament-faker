<?php

use FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures\PostResource;

it('returns only fields in only fields array', function () {
    $resourceFaker = PostResource::faker()->onlyFields('safe_email', 'title');

    expect(array_keys($resourceFaker->fake()))->toEqual(['safe_email', 'title']);
});
