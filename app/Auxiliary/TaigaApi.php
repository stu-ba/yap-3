<?php

namespace Yap\Auxiliary;

use TZK\Taiga\Taiga;
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
}