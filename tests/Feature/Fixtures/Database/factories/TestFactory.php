<?php

namespace FilamentFaker\Tests\Feature\Fixtures\Database\factories;

class TestFactory extends PostFactory
{
    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            ...parent::definition(),
            'title' => '::title::',
            'content' => '::content::',
        ];
    }
}
