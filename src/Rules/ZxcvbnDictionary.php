<?php

namespace Olssonm\Zxcvbn\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ZxcvbnPhp\Matchers\DictionaryMatch;

class ZxcvbnDictionary implements ValidationRule
{
    public const MESSAGE = 'The :attribute is too similar to another field.';

    protected array $input;

    /**
     * Create a new rule instance.
     */
    public function __construct(?string $input1 = null, ?string $input2 = null)
    {
        $this->input = array_filter([$input1, $input2]);
    }

    public static function handle(): string
    {
        return 'zxcvbn_dictionary';
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $matches = DictionaryMatch::match($value, $this->input);
        $matches = array_values(array_filter($matches, function ($match) {
            return $match->dictionaryName === 'user_inputs';
        }));

        if (count($matches) !== 0) {
            $fail(self::MESSAGE);
        }
    }
}
