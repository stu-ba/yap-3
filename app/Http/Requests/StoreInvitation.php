<?php

namespace Yap\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Yap\Models\Invitation;

class StoreInvitation extends FormRequest
{

    public function authorize()
    {
        return auth()->guard('yap')->user()->can('store', Invitation::class) || auth()->user()
                                                                                      ->can('store', Invitation::class);
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
                'email' => 'required|email|unique:invitations|unique:users',
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
