<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TimeNotGreaterThan implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $timeout;
    public function __construct($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
         // Convert both time strings to timestamps
         $timeStampValue = strtotime($value);
         $timeStampTimeout = strtotime($this->timeout);
 
         // Check if the time is less than or equal to the timeout
         return $timeStampValue <= $timeStampTimeout;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must not be greater than the timeout.';
    }
}
