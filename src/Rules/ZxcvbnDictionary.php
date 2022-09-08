<?php

namespace Olssonm\Zxcvbn\Rules;

use Illuminate\Contracts\Validation\Rule;
use ZxcvbnPhp\Matchers\DictionaryMatch;

class ZxcvbnDictionary implements Rule
{
    protected array $input;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($input1 = null, $input2 = null)
    {
        $this->input = array_filter([$input1, $input2]);
    }

    public static function handle(): string
    {
        return 'zxcvbn_dictionary';
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
        $matches = DictionaryMatch::match($value, $this->input);
        $matches = array_values(array_filter($matches, function ($match) {
            return $match->dictionaryName === 'user_inputs';
        }));

        return count($matches) === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is too simililar to another field.';
    }
}
