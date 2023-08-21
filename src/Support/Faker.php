<?php

declare(strict_types=1);

namespace FilamentFaker\Support;

use Faker\Generator;
use Filament\Forms\Components\Field;
use FilamentFaker\Contracts\Support\RealTimeFactory;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Faker implements RealTimeFactory
{
    protected readonly Generator $faker;

    public function __construct()
    {
        $this->faker = fake();
    }

    public function generate(Field $component): mixed
    {
        if ($this->isDisabledFakerMethod($name = Str::camel($component->getName()))) {
            return null;
        }

        try {
            return $this->faker->{$this->filter($name)};
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    protected function filter(string $name): string
    {
        return match ($name) {
            'login', 'username' => 'userName',

            'email', 'emailaddress', 'email_address' => 'safeEmail',

            'phone', 'telephone', 'telnumber', 'mobile', 'tel' => 'phoneNumber',

            'town' => 'city',

            'postalcode', 'postal_code', 'zipcode', 'zip_code' => 'postcode',

            'province', 'county' => $this->predictCountyType(),

            'currency' => 'currencyCode',

            'website' => 'url',

            'companyname', 'company_name', 'employer' => 'company',

            'title' => 'sentence',

            default => $name,
        };
    }

    /**
     * Predicts county type by locale.
     */
    protected function predictCountyType(): string
    {
        return match ($this->faker->locale) {
            'en_US' => 'city',
            default => 'state'
        };
    }

    /**
     * Check if component name is in the disabled faker method array.
     */
    protected function isDisabledFakerMethod(string $componentName): bool
    {
        $methods = $this->filteredFakerMethods();

        return in_array($componentName, $methods) || in_array(Str::snake($componentName), $methods);
    }

    /**
     * @return array<int, string>
     */
    protected function filteredFakerMethods(): array
    {
        return config('filament-faker.excluded_faker_methods', []);
    }
}
