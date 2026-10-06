<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerAnswered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $answer;
    public $gameId;

    /**
     * Create a new event instance.
     */
    public function __construct($answer, $gameId)
    {
        $this->answer = $answer;
        $this->gameId = $gameId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Si usas canal público:
        return [
            new Channel('game.' . $this->gameId),
        ];
        
        // Si prefieres canal privado (recuerda usar PrivateChannel en vez de Channel):
        // return [
        //     new PrivateChannel('game.' . $this->gameId),
        // ];
    }
}