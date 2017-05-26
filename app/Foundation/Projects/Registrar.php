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


    public function create(array $data, array $leaders, array $participants = []): Project
    {
        /**@var \Yap\Models\Project $project */
        $project               = $this->project->create($data);
        $processedLeaders      = $this->processEmails($leaders);
        $processedParticipants = $this->processEmails($participants);

        $project->syncMembers($processedLeaders, $processedParticipants);

        return $project;
    }


    private function processEmails(array $emails): array
    {
        foreach ($emails as $email) {
            $userIds[] = $this->invitationRegistrar->invite($email, [], true)->user_id;
        }

        return $userIds;
    }


    public function update(array $data, Project $project, array $leaders, array $participants): bool
    {
        $updated               = $project->update($data);
        $processedLeaders      = $this->processEmails($leaders);
        $processedParticipants = $this->processEmails($participants);

        $project->syncMembers($processedLeaders, $processedParticipants);

        return $updated;
    }
}