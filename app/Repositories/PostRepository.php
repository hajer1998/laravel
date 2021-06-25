<?php


namespace App\Repositories;


use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use MongoDB\BSON\ObjectId;


class PostRepository
{
    public function get($_id)
    {
        $post = Post::firstWhere('_id', $_id);

        if (!$post) {
            throw new \Exception('post not found');
        }

        return $post;
    }

    public function create(string $body, string $imageLink,string $user_id)
    {
        return Post::create([
            'body' => $body,
            'imageLink'=> $imageLink,
            'user_id' => $user_id,
            'deleted_at' => null
        ]);
    }


    /**
     * Edit post
     *
     * @param $id
     * @param $body
     * @return mixed
     */
    public function editPost($id, $body)
    {
        $post = Post::firstWhere('_id',$id);
        if (!$post){
            throw new \Exception('post not found');
        }

        $post->body = $body;
        $post->update();

        return $post;
    }

    /**
     * Delete post
     *
     * @param string $id
     * @return mixed
     */
    public function deletePost(string $id)
    {
        $post = Post::firstWhere('_id',$id);

        if (!$post){
            throw new \Exception('post not found');
        }

        return $post->delete();
    }

    public function markLike ($id, String $userId)
    {
            $post = Post::firstWhere('_id', $id);

            if (!$post) {
                throw new \Exception('post not found');
            }

            Like::create([
                'post_id' => $id,
                'user_id' => $userId
            ]);
            return $post;
        }

    public function dislike ($id, String $userId)
    {
        $post = Post::firstWhere('_id',$id);

        if (!$post){
            throw new \Exception('post not found');
        }
        $post->likes()
            ->where('post_id', $id)
            ->where('user_id', $userId)
            ->delete();
    }


    public function listingPosts(string $userId){
        $posts = Post::query()->orderBy('_id', 'DESC')->get();
        $posts->transform(function ($post, $key) use ($userId){
            $post->likes_count = $post->likes()->count();
            $post->user = User::firstWhere('_id', new ObjectId($post->user_id));
            $post->is_liked= \DB::table('likes')->where('user_id', $userId)
                ->where('post_id', (string) $post->_id)
                ->exists();
            $post->is_owner = $post->user_id == $userId;
            return $post;
        });
        return $posts;
    }

}
