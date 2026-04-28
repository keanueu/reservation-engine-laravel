<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('user');
    }

    public function broadcastOn()
    {
        // Each chat session has its own private channel. Also broadcast a
        // lightweight event on a public/global channel so admins can update
        // session lists in real-time without needing to subscribe to every
        // private session channel.
        return [
            new PrivateChannel('chat.session.' . $this->message->session_id),
            new Channel('chat.global'),
        ];
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }
}
