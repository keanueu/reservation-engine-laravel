<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast as ShouldBroadcastInterface;
use Illuminate\Foundaton\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $alert;

    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    public function broadcastOn()
    {
        // public channel for site-wide alerts
        return new Channel('typhoon-alerts');
    }

    public function broadcastWith()
    {
        return ['alert' => [
            'id' => $this->alert->id ?? null,
            'title' => $this->alert->title ?? null,
            'severity' => $this->alert->severity ?? null,
            'message' => $this->alert->message ?? null,
            'location' => $this->alert->location ?? null,
            'starts_at' => isset($this->alert->starts_at) ? $this->alert->starts_at->toDateTimeString() : null,
        ]];
    }
}
