namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pin;
    public $userId;
    public $preguntaId;

    public function __construct($pin, $userId, $preguntaId)
    {
        $this->pin = $pin;
        $this->userId = $userId;
        $this->preguntaId = $preguntaId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.' . $this->pin),
        ];
    }

    public function broadcastAs(): string
    {
        return 'respuesta.enviada';
    }
}