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

        $processedLeaders      = $this->processEmails($leaders);
        $processedParticipants = $this->makeUnique($processedLeaders, $this->processEmails($participants));

        /**@var \Yap\Models\Project $project */
        $project = $this->project->create($data);
        $project->syncMembers($processedLeaders, $processedParticipants);

        return $project;
    }


    private function processEmails(array $emails): array
    {
        if (empty($emails)) {
            return [];
        }
        //Todo: catch banned user exception
        foreach ($emails as $email) {
            $userIds[] = $this->invitationRegistrar->invite($email, [], true)->user_id;
        }

        return $userIds;
    }


    /**
     * @param $processedLeaders
     * @param $processedParticipants
     *
     * @return array
     */
    private function makeUnique($processedLeaders, $processedParticipants): array
    {
        if ( ! empty($intersect = array_intersect($processedLeaders, $processedParticipants))) {
            $processedParticipants = array_diff($processedParticipants, $intersect);
        }

        return $processedParticipants;
    }


    public function update(array $data, Project $project, array $leaders, array $participants): bool
    {
        $processedLeaders      = $this->processEmails($leaders);
        $processedParticipants = $this->makeUnique($processedLeaders, $this->processEmails($participants));

        /**@var \Yap\Models\Project $updated */
        $updated = $project->update($data);
        $project->syncMembers($processedLeaders, $processedParticipants);

        return $updated;
    }
}