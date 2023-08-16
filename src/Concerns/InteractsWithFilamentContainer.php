<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\ComponentContainer;
use FilamentFaker\Support\FormsMock;

trait InteractsWithFilamentContainer
{
    protected function container(): ComponentContainer
    {
        return ComponentContainer::make(FormsMock::make());
    }
}
