<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            /**
             * @var User $user
             */
            $user = Auth::user();

            $resource = CommentResource::collection($user->comments);

            return response()->json($resource);
        } catch (Throwable $throwable) {
            Log::debug('Comments index error: ' . $throwable->getMessage());
        }

        return response()->json(['error' => 'Something gone wrong.'], 400);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CommentStoreRequest $request
     * @return JsonResponse
     */
    public function store(CommentStoreRequest $request): JsonResponse
    {
        try {
            /**
             * @var User $user
             */
            $user = Auth::user();
            /**
             * @var Post $post
             */
            $post = Post::find($request->get('post_id'));

            $user->comments()->create(
                [
                    'post_id' => $post->id,
                    'author' => $request->get('author'),
                    'content' => $request->get('content')
                ]
            );

            return response()->json(['success' => 'Comment created successfully.']);
        } catch (Throwable $throwable) {
            Log::debug('Comment create error: ' . $throwable->getMessage());
        }

        return response()->json(['success' => 'Something gone wrong.'], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            /**
             * @var Comment $post
             */
            $comment = Comment::find($id);

            if ($comment) {
                $resource = new CommentResource($comment);

                return response()->json($resource);
            }

            return response()->json(['error' => 'Comment not found'], 404);
        } catch (Throwable $throwable) {
            Log::debug('Show Comment error: ' . $throwable->getMessage());
        }

        return response()->json(['error' => 'Something gone wrong.'], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CommentUpdateRequest $request
     * @param  $id
     * @return JsonResponse
     */
    public function update(CommentUpdateRequest $request, $id): JsonResponse
    {
        try {
            /**
             * @var User $user
             */
            $user = Auth::user();
            /**
             * @var Comment $post
             */
            $comment = Comment::find($id);

            if ($comment && $user->can('update', $comment)) {
                $comment->update(
                    [
                        'author' => $request->get('author') ?? $comment->author,
                        'content' => $request->get('content') ?? $comment->content,
                    ]
                );

                return response()->json(['success' => 'Comment updated successfully.']);
            }

            return response()->json(['success' => 'Comment not found.'], 404);
        } catch (Throwable $throwable) {
            Log::debug('Update Comment error: ' . $throwable->getMessage());
        }

        return response()->json(['error' => 'Something gone wrong.'], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            /**
             * @var User $user
             */
            $user = Auth::user();
            /**
             * @var Comment $post
             */
            $comment = Comment::find($id);

            if ($comment && $user->can('delete', $comment)) {
                $comment->delete();

                return response()->json(['success' => 'Comment deleted successfully.']);
            }

            return response()->json(['success' => 'Comment not found.'], 404);
        } catch (Throwable $throwable) {
            Log::debug('Delete Comment error: ' . $throwable->getMessage());
        }

        return response()->json([], 403);
    }
}
