<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Form;

interface FakesResources
{
    public function withForm(Form|string $form = 'form'): static;

    /**
     * @return array<string, mixed>
     */
    public function fake(): array;

    public function getForm(): Form;
}
