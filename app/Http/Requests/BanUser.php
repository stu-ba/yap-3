<?php

namespace Yap\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BanUser extends FormRequest
{

    use AlwaysAuthorize;


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => [
                'required',
                'max:254',
            ],
        ];
    }
}
