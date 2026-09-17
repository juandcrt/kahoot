<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameSessionStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;
}
