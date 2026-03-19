<?php

namespace Olssonm\Zxcvbn;

use Illuminate\Support\ServiceProvider;
use Olssonm\Zxcvbn\Rules\Zxcvbn;
use Olssonm\Zxcvbn\Rules\ZxcvbnDictionary;
use ZxcvbnPhp\Zxcvbn as ZxcvbnPhp;

class ZxcvbnServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('zxcvbn', function () {
            return new ZxcvbnPhp();
        });
    }

    public function boot()
    {
        foreach ([Zxcvbn::class, ZxcvbnDictionary::class] as $rule) {
            $this->app['validator']->extend($rule::handle(), function ($attribute, $value, $parameters) use ($rule) {
                $instance = new $rule(...$parameters);
                $passed = true;
                $instance->validate($attribute, $value, function () use (&$passed) {
                    $passed = false;
                });
                return $passed;
            }, $rule::MESSAGE);
        }
    }
}
