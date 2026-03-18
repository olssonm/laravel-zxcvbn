<?php

namespace Olssonm\Zxcvbn\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ZxcvbnPhp\Zxcvbn as ZxcvbnPhp;

class Zxcvbn implements ValidationRule
{
    public const MESSAGE = 'The :attribute is not strong enough.';

    private int $target;

    /**
     * Create a new rule instance.
     */
    public function __construct(int $target = 5)
    {
        $this->target = $target;
    }

    public static function handle(): string
    {
        return 'zxcvbn';
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $zxcvbn = (new ZxcvbnPhp())->passwordStrength($value);

        if ($zxcvbn['score'] < $this->target) {
            $fail(self::MESSAGE);
        }
    }
}
