<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory, Sluggable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return string[][]
     */
    public function sluggable(): array
    {
        return [
            'link' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return array
     */
    public function getComments(): array
    {
        $comments = [];

        foreach ($this->comments()->get() as $comment) {
            $comments[] = [
                'id' => $comment->id,
                'author' => $comment->author,
                'content' => $comment->content,
            ];
        }

        return $comments;
    }
}
