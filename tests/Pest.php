<?php

use Filament\Forms\Form;
use FilamentFaker\Contracts\Decorators\ComponentDecorator;
use FilamentFaker\Decorators\Component;
use FilamentFaker\Tests\TestCase;
use FilamentFaker\Tests\TestSupport\Resources\ProductResource;
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

function mockForm(): Form
{
    return ProductResource::faker()->getForm();
}
