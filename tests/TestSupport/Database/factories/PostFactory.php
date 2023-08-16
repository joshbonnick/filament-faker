<?php

namespace FilamentFaker\Tests\TestSupport\Database\factories;

use FilamentFaker\Tests\TestSupport\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public static $namespace = 'FilamentFaker\\Tests\\TestSupport\\Database\\';

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->sentence(),
            'raw_content' => [],
        ];
    }
}
