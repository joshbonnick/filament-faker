<?php

namespace FilamentFaker\Tests\Feature\Fixtures;

use FilamentFaker\Tests\Feature\Fixtures\Models\Post;
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
        return ['foo' => 'bar', 'baz' => 'hello', 'world' => 'foobar'];
    }
}
