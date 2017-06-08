<?php

namespace Yap\Foundation\Validators;

class CurrentUserPresent
{

    public function validate($attribute, $value, $parameters, $validator)
    {
        //TODO: refactor me, move around some code and reduce it to 10 lines
        //dd($parameters,count($parameters));
        //if (count($parameters) != 2 || ) {
        //    throw new \InvalidArgumentException('Validation rule needs zero or two parameters.');
        //}

        /** @var \Yap\Models\User $user */
        $user = auth()->user();
        if ($user->is_admin && $parameters[0].$parameters[1] === 'exceptadmin') {
            return true;
        }
        //grab all users emails

        /** @var \Illuminate\Support\Collection $value */
        $value = collect($value);
        if ($value->contains($user->email) || $value->intersect($user->invitations->pluck('email'))->isEmpty()) {
            return true;
        }


        return false;
    }


    public function replace($message, $attribute, $rule, $parameters)
    {
        return str_replace(':parameter', str_slug($parameters[0]), $message);
    }

}