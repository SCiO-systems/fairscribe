<?php

namespace App\Http\Requests\TeamResources;

use App\Enums\ResourceType;
use App\Rules\ResourceTypeValidationRule;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTeamResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization parameters.
        $isLoggedIn = Auth::check();
        $isTeamMember = !empty(Auth::user()->sharedTeams()->find($this->team->id));
        $isTeamOwner = $this->team->owner_id === Auth::user()->id;
        return $isLoggedIn && ($isTeamMember || $isTeamOwner);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => ['required', 'string', new ResourceTypeValidationRule],
            'authoring_team' => 'array|required',
            'review_team' => 'array|required',
            'collections' => 'nullable|array',
        ];
    }
}