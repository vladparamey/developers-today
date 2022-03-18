<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * PostResource constructor.
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->resource = $post;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'link' => $this->resource->link,
            'amount_upvotes' => $this->resource->amount_upvotes,
            'author' => $this->resource->author,
            'comments' => $this->resource->getComments(),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
