<?php

namespace App\Rules;

use App\Enums\ResourceType;
use Illuminate\Contracts\Validation\Rule;

class ResourceTypeValidationRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, ResourceType::getValues()) || empty($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be one of the following: ' . implode(
            ', ',
            ResourceType::getValues()
        );
    }
}
