<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $posts = Post::all();

            $resource = PostResource::collection($posts);

            return response()->json($resource);

        } catch (Throwable $throwable) {
            Log::debug('Posts index error: ' . $throwable->getMessage());
        }

        return response()->json(['error' => 'Something gone wrong.'], 400);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostStoreRequest $request
     * @return JsonResponse
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $user->posts()->create([
                'title' => $request->get('title'),
                'link' => SlugService::createSlug(Post::class, 'link', $request->get('title')),
                'author' => $request->get('author') ?? $user->name
            ]);

            return response()->json(['success' => 'Post created successfully.']);

        } catch (Throwable $throwable) {
            Log::debug('Post create error: ' . $throwable->getMessage());
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        try {
            /** @var Post $post */
            $post = Post::where('link', $slug)->first();

            if ($post) {
                $resource = new PostResource($post);

                return response()->json($resource);
            }

            return response()->json(['error' => 'Post not found'], 404);

        } catch (Throwable $throwable) {
            Log::debug('Show Post error: ' . $throwable->getMessage());
        }

        return response()->json(['error' => 'Something gone wrong.'], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PostUpdateRequest $request, $id): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            /** @var Post $post */
            $post = Post::find($id);

            if ($post && $user->can('update', $post)) {
                $post->update([
                    'title' => $request->get('title') ?? $post->title,
                    'link' => SlugService::createSlug(
                        Post::class, 'link', $request->get('title') ?? $post->title
                    ),
                    'author' => $request->get('author') ?? $post->author
                ]);

                return response()->json(['success' => 'Post updated successfully.']);
            }

            return response()->json(['success' => 'Post not found.'], 403);

        } catch (Throwable $throwable) {
            Log::debug('Update Post error: ' . $throwable->getMessage());
        }

        return response()->json(['error' => 'Something gone wrong.'], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            /** @var Post $post */
            $post = Post::find($id);

            if ($post && $user->can('delete', $post)) {
                $post->delete();

                return response()->json(['success' => 'Post deleted successfully.']);
            }

            return response()->json(['success' => 'Post not found.'], 404);

        } catch (Throwable $throwable) {
            Log::debug('Delete Post error: ' . $throwable->getMessage());
        }

        return response()->json([], 403);
    }
}
