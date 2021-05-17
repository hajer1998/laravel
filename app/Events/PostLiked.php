<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PostLiked
{
    use Dispatchable;

    public $postId;
    public $userName;
    public $userId;
    public $postOwnerId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($postId, $userName, $userId, $postOwnerId)
    {
        $this->postId = $postId;
        $this->userName = $userName;
        $this->userId = $userId;
        $this->postOwnerId = $postOwnerId;
    }
}
