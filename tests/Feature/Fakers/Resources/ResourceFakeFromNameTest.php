<?php

use FilamentFaker\Tests\TestSupport\Resources\ProductResource;

it('can disable the usage of faking by component name by chaining', function () {
    mockComponentDecorator();

    $data = ProductResource::faker()->shouldFakeUsingComponentName(false)->fake();

    expect($data['safe_email'])
        ->not
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');

    $data = ProductResource::faker()->shouldFakeUsingComponentName(true)->fake();

    expect($data['safe_email'])
        ->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});
