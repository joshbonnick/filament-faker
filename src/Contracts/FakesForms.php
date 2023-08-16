<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Form;

interface FakesForms
{
    /**
     * @return array<string, mixed>
     */
    public function fake(Form $form, bool $withHidden = false): array;
}
