<?php

namespace Yap\Auxiliary;

use TZK\Taiga\Exceptions\TaigaException;
use TZK\Taiga\Taiga;
use Yap\Models\Project;
use Yap\Models\User;

class TaigaApi
{

    protected $taiga;


    public function __construct(Taiga $taiga)
    {
        $this->taiga = $taiga;
        $this->taiga->setAuthToken(taiga_token(systemAccount()->taiga_id));
    }


    public function roleChange(User $user)
    {
        $this->taiga->users()->editPartially($user->taiga_id, ['is_superuser' => $user->is_admin]);
    }


    public function createUser(User $user)
    {
        return $this->taiga->users()->create([
            'username'  => $user->username,
            'email'     => $user->email,
            'full_name' => $user->name ?? 'Anonymous',
            //'photo'     => $user->avatar, //TODO: is not used while creating user
            'bio'       => $user->bio ?? 'I keep my secrets.',
        ]);
    }


    public function createProject(Project $project)
    {
        return $this->taiga->projects()->create([
            'name'              => $project->name,
            'description'       => $project->description,
            'creation_template' => $project->project_type_id,
            'is_private'        => false,
        ]);
    }


    public function getProjectById(int $id): ?\stdClass
    {
        try {
            return $this->taiga->projects()->getById($id);
        } catch (TaigaException $e) {
            return null;
        }
    }


    public function getTypes()
    {
        return $this->taiga->projectTemplates()->getList();
    }
}