<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function index()
    {
        return $this->apiResponse(Post::all(), 200, 'Posts retrieved successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return $this->apiResponse($post, 201, 'Post created successfully');
    }

    public function show($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return $this->apiResponse(null, 404, 'Post not found');
        }

        return $this->apiResponse($post, 200, 'Post retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return $this->apiResponse(null, 404, 'Post not found');
        }

        if (!Gate::allows('update', $post)) {
            return $this->apiResponse(null, 403, 'Unauthorized: You do not own this post to update.');
        }

        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return $this->apiResponse($post, 200, 'Post updated successfully');
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return $this->apiResponse(null, 404, 'Post not found');
        }

        if (!Gate::allows('delete', $post)) {
            return $this->apiResponse(null, 403, 'Unauthorized: You do not own this post to delete.');
        }

        $deletedPost = $post;
        $post->delete();

        return $this->apiResponse($deletedPost, 200, 'Post deleted successfully');
    }
}
