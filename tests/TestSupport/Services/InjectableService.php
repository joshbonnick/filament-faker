<?php

namespace FilamentFaker\Tests\TestSupport\Services;

use FilamentFaker\Tests\TestSupport\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class InjectableService
{
    /**
     * @return Collection<int, Post>
     */
    public function get(): Collection
    {
        return Post::query()->select(['id', 'title'])->get();
    }

    public function search(): array
    {
        return ['foo', 'bar', 'baz', 'hello', 'world'];
    }
}
