<?php

namespace Yap\Foundation\Projects;

use Yap\Foundation\InvitationRegistrar;
use Yap\Models\Project;

class Registrar
{

    /**
     * @var \Yap\Models\Project
     */
    private $project;

    /**
     * @var \Yap\Foundation\InvitationRegistrar
     */
    private $invitationRegistrar;


    public function __construct(Project $project, InvitationRegistrar $invitationRegistrar)
    {
        $this->project             = $project;
        $this->invitationRegistrar = $invitationRegistrar;
    }


    public function create(array $data, array $leaders, array $participants = [])
    {
        /**@var \Yap\Models\Project $project */
        $project               = $this->project->create($data);
        $processedLeaders      = $this->processEmails($leaders);
        $processedParticipants = $this->processEmails($participants);

        $project->addLeaders($processedLeaders);
        $project->addParticipants($processedParticipants);
    }


    private function processEmails(array $emails): array
    {
        foreach ($emails as $email) {
            $user_ids[] = $this->invitationRegistrar->invite($email, [], true)->user_id;
        }

        return $user_ids;
    }


    public function update(Project $project, array $leaders, array $participants = [])
    {
        //TODO: me
    }
}