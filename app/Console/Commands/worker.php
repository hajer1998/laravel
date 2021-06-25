<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class worker extends Command
{

    const QUEUE_NAME = 'hellolaravel';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start consuming messages';

    /**
     *
     * @var Client
     */
    private $httpClient;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'http://localhost:8000/api',
            'timeout' => 10
        ]);
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection('172.17.0.1', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare(self::QUEUE_NAME, false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function (AMQPMessage $msg) {
            var_dump('[x] Received ' . $msg->body);
            $payload = json_decode($msg->getBody(), true);
            $method = $payload['method'];
            $route = $payload['route'];
            $headers = $payload['headers'];
            $body = $payload['body'];
            $query = $payload['query'];

            try {
                // handle request
                $request = $this->httpClient->request($method, $route, [
                    'headers' => $headers,
                    'json' => $body,
                    'query' => $query
                ]);

                $response = $request->getBody()->getContents();

            } catch (\Throwable $exception) {
                $response = json_encode([
                    'hasError' => true,
                    'message' => $exception->getMessage(),
                    'status' => $exception->getCode() ?: 500
                ]);
            }
            //
            /** @var AMQPChannel $amqpRequest */
            $amqpRequest = $msg->delivery_info['channel'];
            $amqpRequest->basic_publish(new AMQPMessage($response, [
                'correlation_id' => $msg->get('correlation_id'),
                'reply_to' => $msg->get('reply_to')
            ]), '', $msg->get('reply_to'));
        };


        $channel->basic_consume(self::QUEUE_NAME, '', false, true, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return 0;
    }
}
#command prompt: php artisan rabbitmq:consume
