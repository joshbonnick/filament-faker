<?php

declare(strict_types=1);

namespace FilamentFaker\Contracts;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

interface FakesComponents
{
    /**
     * Generates mock data for a Filament component.
     */
    public function fake(): mixed;

    public function setUpComponent(Field $component): Field;

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
