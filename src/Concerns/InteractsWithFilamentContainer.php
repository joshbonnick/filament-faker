<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;

trait InteractsWithFilamentContainer
{
    protected function container(): ComponentContainer
    {
        return ComponentContainer::make(resolve(HasForms::class));
    }
}
