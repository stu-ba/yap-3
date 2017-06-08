<?php

namespace Yap\Foundation\Validators;

class CurrentUserPresent
{

    /**
     * Rule uses validation parameters only to make it understandable from first glance.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     *
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        /** @var \Yap\Models\User $user */
        $user = auth()->user();
        if ($user->is_admin && $parameters[0].$parameters[1] === 'skipadmin') {
            return true;
        }

        /** @var \Illuminate\Support\Collection $value */
        $value = collect($value);
        if ($value->contains($user->email) || $value->intersect($user->invitations->pluck('email'))->isEmpty()) {
            return true;
        }

        return false;
    }
}