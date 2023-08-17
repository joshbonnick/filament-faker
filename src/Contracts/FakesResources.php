<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

interface FakesResources
{
    public function withForm(Form|string $form = 'form'): static;

    /**
     * @return array<string, mixed>
     */
    public function fake(): array;

    /**
     * @param  class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(string $factory = null): static;

    public function getForm(): Form;

    public function shouldFakeUsingComponentName(bool $should = true): static;

    public function mutateFake(Field $component): static;
}
