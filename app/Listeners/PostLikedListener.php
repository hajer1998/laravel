<?php

namespace App\Listeners;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PostLikedListener
{
    private $channel;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'),
            env('RABBITMQ_PASSWORD')
        );
        $this->channel = $connection->channel();
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $message = new AMQPMessage(json_encode([
            'message_type' => 'notification',
            'post_id' => $event->postId,
            'user_name' => $event->userName,
            'user_id' => $event->userId,
            'post_owner_id' => $event->postOwnerId
        ]));

        $this->channel->basic_publish($message, null, 'nodejs');
    }
}
