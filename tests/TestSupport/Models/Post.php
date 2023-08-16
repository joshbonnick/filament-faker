<?php

namespace FilamentFaker\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    /**
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'raw_content' => 'array',
    ];

    /**
     * @return BelongsTo<Post, Post>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'parent_id', 'id');
    }
}
