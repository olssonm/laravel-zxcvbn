<?php

namespace Olssonm\Zxcvbn\Facades;

use Illuminate\Support\Facades\Facade;

class Zxcvbn extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'zxcvbn';
    }
}
