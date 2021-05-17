<?php


namespace App\Http\Controllers;

use App\Events\PostLiked;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController
{
    /**
     * @var PostRepository
     */
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * create a post
     */
    public function create(Request $request)
    {
        try {
            $post = $this->repository->create(
                $request->get('body'),
                $request->get('user_id')
            );
        } catch (\Throwable $exception) {
            throw new \Exception('post not created');
        }

        return response()->json($post->toArray());
    }

    /**
     *Edit post
     */
    public function edit(Request $request, $id)
    {
        try {
            $post = $this->repository->editPost($id, $request->get('body'));
        } catch (\Throwable $exception) {
            return \response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }
    }


    /**
     * delete a post
     */
    public function delete($id)
    {
        try {
            $this->repository->deletePost($id);
        } catch (\Throwable $exception) {
            return \response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response(null, 204);
    }

    public function like($id, Request $request)
    {
        try {
            $post = $this->repository->markLike(
                $id,
                $request->get('user_id')
            );
//            if ($post->user_id != $request->get('user_id')) {
                PostLiked::dispatch(
                    $id,
                    User::firstWhere('_id', $request->get('user_id'))->name,
                    $request->get('user_id'),
                    $post->user_id
                );
//            }
            return response(null, 204);
        } catch (\Throwable $exception){
            return response()->json('post not liked', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function unlike($id, Request $request)
    {
        try {
            $like = $this->repository->dislike($id, $request->get('user_id'));
        } catch (\Throwable $exception){
            throw new \Exception('unlike not marked');
        }

        return response(null,204);
    }

    /**
     * listing Posts
     */
    public function listing(Request $request)
    {
       $posts= $this->repository->listingPosts($request->get('user_id'));

        if (empty($posts)) {
            return \response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $posts
        ]);
    }
    /**
     * getPost
     */

    public function get($id)
    {
        try {
            $post = $this->repository->get($id);
        } catch (\Exception $exception) {
            exit('post not found');
        }

        return response()->json($post->toArray());
    }
}
