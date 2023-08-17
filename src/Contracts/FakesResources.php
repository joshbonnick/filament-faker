<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Form;

interface FakesResources
{
    /**
     * Specify which form to fake, if there is only one form on the page then
     * you should use the default parameter.
     *
     * If you have renamed the static 'form' method, that should be reflected
     * here.
     */
    public function withForm(Form|string $form = 'form'): static;

    /**
     * Generates mock data array for an entire Filament form attached to a resource.
     *
     * @return array<string, mixed>
     */
    public function fake(): array;

    /**
     * Returns an instance of the Form that will be used to generate mock data.
     */
    public function getForm(): Form;
}
