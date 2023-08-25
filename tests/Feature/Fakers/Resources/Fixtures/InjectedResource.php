<?php

namespace FilamentFaker\Tests\Feature\Fakers\Resources\Fixtures;

class InjectedResource extends PostResource
{
    public function __construct(string $foo)
    {

    }
}
