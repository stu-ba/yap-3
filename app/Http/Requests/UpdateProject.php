<?php

namespace Yap\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Yap\Models\Project;

class UpdateProject extends FormRequest
{
    use AlwaysAuthorize;

    public function all()
    {
        $attributes                      = parent::all();
        $attributes['team_leaders']      = array_unique(extractEmails($attributes['team_leaders']) ?? []);
        $attributes['participants']      = array_unique(extractEmails($attributes['participants']) ?? []);
        $attributes['create_repository'] = $attributes['create_repository'] ?? false;

        if ($this->user()->cannot('archive', Project::class)) {
            unset($attributes['archive_at']);
        }

        return $attributes;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description'  => 'required',
            'archive_at'   => 'nullable|date_format:d/m/Y|after_or_equal:yesterday',
            'team_leaders' => 'required|current_user_present:except,admin',
            'participants' => 'array_unique:team_leaders',
        ];
    }


    public function messages()
    {
        return [
            'team_leaders.required'     => 'The team leaders field is required (invalid emails were removed).',
            'archive_at.after_or_equal' => 'Pick a date that is in future (or today).',
        ];
    }
}
