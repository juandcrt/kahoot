<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RespuestaEnviada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pin;
    public $userId;
    public $preguntaId;
    public $puntos;
    public $tiempoMs;
    public $esCorrecta;

    public function __construct($pin, $userId, $preguntaId, $puntos, $tiempoMs, $esCorrecta)
    {
        $this->pin = $pin;
        $this->userId = $userId;
        $this->preguntaId = $preguntaId;
        $this->puntos = $puntos;
        $this->tiempoMs = $tiempoMs;
        $this->esCorrecta = $esCorrecta;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.' . $this->pin)
        ];
    }

    public function broadcastAs(): string
    {
        return 'RespuestaEnviada';
    }

    public function broadcastWith(): array
    {
        return [
            'userId' => $this->userId,
            'user_id' => $this->userId,
            'preguntaId' => $this->preguntaId,
            'pregunta_id' => $this->preguntaId,
            'puntos' => $this->puntos,
            'tiempoMs' => $this->tiempoMs,
            'esCorrecta' => $this->esCorrecta,
        ];
    }
}