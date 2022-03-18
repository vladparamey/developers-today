<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * PostResource constructor.
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->resource = $comment;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'post_id' => $this->resource->post_id,
            'user_id' => $this->resource->user_id,
            'author' => $this->resource->author,
            'content' => $this->resource->content,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
