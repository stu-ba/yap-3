<?php

namespace Yap\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Yap\Models\Project;

class ProjectCreated
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
