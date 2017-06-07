<?php

namespace Yap\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageUser extends FormRequest
{

    use AlwaysAuthorize;


    public function all()
    {
        $attributes         = parent::all();
        $attributes['user'] = $this->route()->parameter('user')->id;

        return $attributes;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'user' => 'required|not_current_user',
        ];

        //handle special case: banning user requires also reason field
        $rules = $this->applyBanRules($rules);

        return $rules;
    }

    public function messages()
    {
        return ['user.not_current_user' => 'You can not do that to yourself!'];
    }


    /**
     * @param $rules
     *
     * @return array
     */
    private function applyBanRules($rules): array
    {
        if ($this->isMethod('patch') && $this->is('*/ban')) {
            $rules = array_merge($rules, [
                'reason' => [
                    'required',
                    'max:254',
                ],
            ]);
        }

        return $rules;
    }

}
