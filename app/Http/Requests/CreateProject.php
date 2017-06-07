<?php

namespace Yap\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProject extends FormRequest
{

    use AlwaysAuthorize;


    public function all()
    {
        $attributes                      = parent::all();
        $attributes['team_leaders']      = array_unique(extractEmails($attributes['team_leaders']) ?? []);
        $attributes['participants']      = array_unique(extractEmails($attributes['participants']) ?? []);
        $attributes['create_repository'] = $attributes['create_repository'] ?? false;

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
            'name'            => 'required|max:32|unique:projects,name|repository_unique',
            'description'     => 'required',
            'project_type_id' => 'required|exists:project_types,id',
            'archive_at'      => 'nullable|date_format:d/m/Y|after_or_equal:yesterday',
            'team_leaders'    => 'required',
            'participants'    => 'array_unique:team_leaders',
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
