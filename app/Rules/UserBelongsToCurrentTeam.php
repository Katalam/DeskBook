<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UserBelongsToCurrentTeam implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! request()?->user()->currentTeam->allUsers()->pluck('id')->contains($value)) {
            $fail('The user does not belong to the current team.');
        }
    }
}
