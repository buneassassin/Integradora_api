<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Sensor;
use Illuminate\Support\Facades\Log;

class Sensores implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sensor;

    public function __construct($sensor)
    {
        $this->sensor = (object) $sensor;
    }

    public function broadcastOn()
    {
        return ['reviews'];
    }

    public function broadcastAs()
    {
        $eventName = 'Sensor_'. $this->sensor->sensor_id .'_Data_' . $this->sensor->tinaco_id;
        Log::info('Broadcast event name: ' . $eventName);
        return $eventName;
    }
    public function broadcastWith()
    {
        return [
            'sensor' => $this->sensor
        ];
    }
}
