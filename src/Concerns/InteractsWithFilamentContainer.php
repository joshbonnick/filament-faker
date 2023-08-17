<?php

declare(strict_types=1);

namespace FilamentFaker\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use FilamentFaker\Support\FormsMock;

trait InteractsWithFilamentContainer
{
    public function setUpComponent(Field $component): Field
    {
        return tap($component)->container($this->container());
    }

    protected function container(): ComponentContainer
    {
        return ComponentContainer::make(FormsMock::make());
    }
}
