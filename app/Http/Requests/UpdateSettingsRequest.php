<?php

namespace Traq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Traq\Permissions;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasPermission(Permissions::PERMISSION_ADMIN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'traq_name' => 'required',
        ];
    }
}
