<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Closure;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * Generate fake data using model factories.
     *
     * @param  array<int, string>  $onlyAttributes
     * @param  Factory<Model>|class-string<Factory<Model>>|null  $factory
     */
    public function withFactory(Factory|string $factory = null, array $onlyAttributes = []): static;

    /**
     * Enable or disable attempts to use component names to retrieve methods
     * from FakerPHP
     */
    public function shouldFakeUsingComponentName(bool $should = true): static;

    /**
     * Add a callback function to the Faker instance for more control over the
     * output of mock data.
     */
    public function mutateFake(Closure $callback = null): static;
}
