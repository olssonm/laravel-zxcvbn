<?php

namespace Olssonm\Zxcvbn;

use ZxcvbnPhp\Zxcvbn as ZxcvbnPhp;
use Illuminate\Support\ServiceProvider;
use Validator;

class ZxcvbnServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Extend the Laravel Validator with the "zxcvbn_min" rule
         */
        Validator::extend('zxcvbn_min', function($attribute, $value, $parameters, $validator) {
            $zxcvbn = new ZxcvbnPhp();
            $zxcvbn = $zxcvbn->passwordStrength($value);
            $target = 5;

            if(isset($parameters[0])) {
                $target = $parameters[0];
            }

            return ($zxcvbn['score'] >= $target);
        }, 'Your :input is not secure enough.');

        Validator::replacer('zxcvbn_min', function($message, $attribute, $rule, $parameters) {
            $message = str_replace(':input', $attribute, $message);
            return $message;
        });

        /**
         * Extend the Laravel Validator with the "zxcvbn_min" rule
         */
        Validator::extend('zxcvbn_dictionary', function($attribute, $value, $parameters, $validator) {
            $email = null;
            $username = null;

            if(isset($parameters[0])) {
                $email = $parameters[0];
                $username = $parameters[1];
            }

            $zxcvbn = new ZxcvbnPhp();
            $zxcvbn = $zxcvbn->passwordStrength($value, [$username, $email]);

            if(isset($zxcvbn['match_sequence'][0])) {
                $dictionary = $zxcvbn['match_sequence'][0];
                if(isset($dictionary->dictionaryName)) {
                    return false;
                }
            }

            return true;

        }, 'Your :input is insecure. It either matches a commonly used password, or you have used a similar username/password combination.');

        Validator::replacer('zxcvbn_dictionary', function($message, $attribute, $rule, $parameters) {
            $message = str_replace(':input', $attribute, $message);
            return $message;
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('zxcvbn', function ($app) {
            return new ZxcvbnPhp();
        });
    }
}
