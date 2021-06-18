<?php

namespace App\Http\Requests\UserTeamInvites;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UserTeamInviteCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if the user who sends the invite is a team owner.
        $isSameUser = Auth::user()->id === $this->user->id;
        $isTeamOwner = $this->team->owner_id === $this->user->id;
        return $isSameUser && $isTeamOwner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email'
        ];
    }
}
