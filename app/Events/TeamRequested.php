<?php

namespace Yap\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Yap\Models\Project;

class TeamRequested
{
    use Dispatchable, SerializesModels;

    /**
     * @var \Yap\Models\Project
     */
    public $project;


    public function __construct(Project $project)
    {
        $this->project = $project;
    }
}
