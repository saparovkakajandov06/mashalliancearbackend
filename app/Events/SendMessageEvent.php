<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $chat;
    
    /**
     * Create a new event instance.
     *
     * @param $chat
     * @param $message
     */
    public function __construct($chat, $message)
    {
        $this->message = $message;
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['new-message-'. $this->chat->id];
    }
    
    public function broadcastAs()
    {
        return 'new-message';
    }
}
