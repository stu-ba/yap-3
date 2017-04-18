<?php

namespace Yap\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvitation extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->isXmlHttpRequest()) {
            return [
                'email' => 'required|email|unique:invitations',
            ];
        }

        return [
            'email' => 'required|email',
        ];

    }


    public function messages()
    {
        return [
            'email.unique' => 'Invitation with this email already exists.',
        ];
    }
}
