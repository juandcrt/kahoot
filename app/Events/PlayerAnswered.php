<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

// 广播给 Host
class PlayerAnswered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;
}
