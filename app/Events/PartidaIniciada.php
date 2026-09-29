<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PartidaIniciada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pinSala;

    public function __construct($pinSala)
    {
        $this->pinSala = $pinSala;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.' . $this->pinSala),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PartidaIniciada';
    }
}