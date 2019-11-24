<?php
/*!
 * Traq
 *
 * Copyright (C) 2009-2019 Jack P.
 * Copyright (C) 2012-2019 Traq.io
 * https://github.com/nirix
 * https://traq.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Traq\UserGroup;

class StoreUserGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $userGroup = UserGroup::find($this->route('user_group'));

        return (
            ($userGroup && $this->user()->can('update', $userGroup->first())) ||
            (!$userGroup && $this->user()->can('create', UserGroup::class))
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:user_groups']
        ];
    }
}
