<?php

namespace Yap\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveProject extends FormRequest
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
            'archive_at' => 'required|date_format:U|after_or_equal:today',
        ];
    }


    public function messages()
    {
        return [
            'archive_at.required'       => 'Date is required!',
            'archive_at.after_or_equal' => 'Pick a date that is in future (or today).',
        ];
    }
}
