<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class SendDeletionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filename;

    /**
     * Create a new job instance.
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // RabbitMQ connection details from .env
            $host = env('RABBITMQ_HOST', '127.0.0.1');
            $port = env('RABBITMQ_PORT', 5672);
            $user = env('RABBITMQ_USER', 'guest');
            $password = env('RABBITMQ_PASSWORD', 'guest');
            $queueName = env('RABBITMQ_QUEUE', 'file_deletion_queue');

            // Establish RabbitMQ connection
            $connection = new AMQPStreamConnection($host, $port, $user, $password);
            $channel = $connection->channel();

            // Declare queue
            $channel->queue_declare($queueName, false, true, false, false);

            // Message to send
            $messageBody = json_encode([
                'message' => "File '{$this->filename}' has been deleted.",
                'timestamp' => now()->toDateTimeString()
            ]);

            $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

            // Publish message to queue
            $channel->basic_publish($message, '', $queueName);

            // Close channel and connection
            $channel->close();
            $connection->close();

            Log::info("RabbitMQ Notification Sent for File Deletion: {$this->filename}");

        } catch (\Exception $e) {
            Log::error("RabbitMQ Error: " . $e->getMessage());
        }
    }
}
