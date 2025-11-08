<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message->load('sender', 'conversation');
    }

    public function broadcastOn()
    {
        $channels = [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
 
        $conversation = $this->message->conversation;
        $receiverId = $this->message->sender_id === $conversation->user_one_id 
            ? $conversation->user_two_id 
            : $conversation->user_one_id;
        
        $channels[] = new PrivateChannel('user.' . $receiverId);

        return $channels;
    }

    public function broadcastWith()
    {
       return [
            'message' => [
                'id' => $this->message->id,
                'body' => $this->message->body,
                'sender_id' => $this->message->sender_id,
                'sender' => $this->message->sender->only(['id', 'name']),
                'conversation_id' => $this->message->conversation_id,
                'created_at' => $this->message->created_at->toDateTimeString(),
            ],
        ];
    }
}
