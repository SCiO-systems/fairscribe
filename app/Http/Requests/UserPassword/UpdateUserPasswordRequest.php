<?php

namespace App\Http\Requests\UserPassword;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isLoggedIn = Auth::check();
        $isSameUser = Auth::user()->id == $this->user->id;
        return $isLoggedIn && $isSameUser;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|string|min:8',
            'new' => 'required|string|min:8',
        ];
    }
}
