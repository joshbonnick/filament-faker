<?php

namespace FilamentFaker\Tests\TestSupport\Models;

use FilamentFaker\Tests\TestSupport\Database\factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected static function newFactory(): PostFactory
    {
        return new PostFactory();
    }

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
