<?php

namespace FilamentFaker\Tests\TestSupport\Database\factories;

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
