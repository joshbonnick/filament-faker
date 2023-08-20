<?php

use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Decorators\Component;
use FilamentFaker\Tests\TestCase;
use Mockery\MockInterface;

uses(TestCase::class)->in(__DIR__);

function mockComponentDecorator(MockInterface $mock = null)
{
    if (! $mock) {
        $mock = mock(Component::class)->makePartial();
        $mock->shouldReceive('getSearch')->andReturn([1, 2, 3]);
    }

    app()->instance(ComponentDecorator::class, $mock);
}
