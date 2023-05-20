<?php

namespace Olssonm\Zxcvbn\Rules;

use Illuminate\Contracts\Validation\Rule;
use ZxcvbnPhp\Zxcvbn as ZxcvbnPhp;

class Zxcvbn implements Rule
{
    private $target;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($target = 5)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public static function handle()
    {
        return 'zxcvbn';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $zxcvbn = (new ZxcvbnPhp())->passwordStrength($value);
        return ($zxcvbn['score'] >= $this->target);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not strong enough.';
    }
}
