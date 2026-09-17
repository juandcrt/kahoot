<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

// 广播给 Player
class ScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;
}
