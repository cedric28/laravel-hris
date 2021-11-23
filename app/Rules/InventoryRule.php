<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class InventoryRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $sellingPrice;

    public function __construct($sellingPrice)
    {
        $this->sellingPrice = $sellingPrice;
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
        return $value > $sellingPrice;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be greater than .';
    }
}
