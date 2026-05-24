<?php

namespace App\Http\Controllers;
// use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    // public function index(Request $request): JsonResponse
    // {
    //     $userId = $request->user()->id;
    //     $rows = DB::select('SELECT * FROM posts WHERE user_id = ?', [$userId]);
    //     return response()->json($rows);
    // }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Post::class);

        $posts = Post::query()
            ->with('user:id,name,email')
            ->latest()
            ->get()
            ->map(function (Post $post) use ($request) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'body' => $post->body,
                    'created_at' => $post->created_at?->format('Y-m-d H:i'),
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                        'email' => $post->user->email,
                    ],
                    'can_update' => $request->user()->can('update', $post),
                    'can_delete' => $request->user()->can('delete', $post),
                ];
            });

        return response()->json([
            'data' => $posts,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Post::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string'],
        ]);

        $post = $request->user()->posts()->create($validated);

        return response()->json([
            'data' => $post,
        ], 201);
    }

    public function show(Request $request, Post $post): JsonResponse
    {
        Gate::authorize('view', $post);

        $post->load('user:id,name,email');

        return response()->json([
            'data' => $post,
        ]);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string'],
        ]);

        $post->update($validated);

        return response()->json([
            'data' => $post,
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => '삭제되었습니다.',
        ]);
    }
}
