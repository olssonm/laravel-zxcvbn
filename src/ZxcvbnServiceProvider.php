<?php

namespace Olssonm\Zxcvbn;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\ServiceProvider;
use Olssonm\Zxcvbn\Rules\Zxcvbn;
use Olssonm\Zxcvbn\Rules\ZxcvbnDictionary;

use Validator;
use ZxcvbnPhp\Zxcvbn as ZxcvbnPhp;

class ZxcvbnServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('zxcvbn', function() {
            return new ZxcvbnPhp();
        });
    }

    public function boot()
    {
        foreach ([Zxcvbn::class, ZxcvbnDictionary::class] as $rule) {
            $this->app['validator']->extend($rule::handle(), function ($attribute, $value, $parameters) use ($rule) {
                return (new $rule(...$parameters))->passes($attribute, $value);
            },
            (new $rule)->message());
        }
    }
}
