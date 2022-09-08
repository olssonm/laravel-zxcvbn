# Zxcvbn for Laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total downloads][ico-downloads]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://img.shields.io/github/workflow/status/olssonm/laravel-zxcvbn/Laravel%20automated%20tests?style=flat-square)](https://github.com/olssonm/ampersand/actions/workflows/test.yml)

![zxcvbn](https://user-images.githubusercontent.com/907114/41193108-747d9b50-6c08-11e8-8f9c-57874f52fa9b.png)

A simple implementation of zxcvbn for Laravel. This package allows you to access "zxcvbn-related" data on a passphrase in the application and also to use zxcvbn as a standard validator.

Uses [Zxcvbn-PHP](https://github.com/bjeavons/zxcvbn-php) by [@bjeavons](https://github.com/bjeavons), which in turn is inspired by [zxcvbn](https://github.com/dropbox/zxcvbn) by [@dropbox](https://github.com/dropbox).

## Install

Via Composer

```bash
$ composer require olssonm/l5-zxcvbn
```

If you wish to have the ability to use `Zxcvbn` via dependency injection, or just have a quick way to access the class – add an alias to the facades:

```php
'aliases' => [
    'Zxcvbn' => Olssonm\Zxcvbn\Facades\Zxcvbn::class
]
```

## Usage

If you've added `Olssonm\Zxcvbn` as an alias, your can access Zxcvbn easily from anywhere in your application:

### "In app"

``` php
use Zxcvbn;

class MyClass extends MyOtherClass
{
    public function myFunction()
    {
        $zxcvbn = Zxcvbn::passwordStrength('password');
        dd($zxcvbn);

        // array:9 [
        //     "password" => "password"
        //     "guesses" => 3.0
        //     "guesses_log10" => 0.47712125471966
        //     "sequence" => [],
        //     "crack_times_seconds" => array:4 [
        //         "online_throttling_100_per_hour" => 108.0
        //         "online_no_throttling_10_per_second" => 0.3
        //         "offline_slow_hashing_1e4_per_second" => 0.0003
        //         "offline_fast_hashing_1e10_per_second" => 3.0E-10
        //     ]
        //     "crack_times_display" => array:4 [
        //         "online_throttling_100_per_hour" => "2 minutes"
        //         "online_no_throttling_10_per_second" => "less than a second"
        //         "offline_slow_hashing_1e4_per_second" => "less than a second"
        //         "offline_fast_hashing_1e10_per_second" => "less than a second"
        //     ]
        //     "score" => 0
        //     "feedback" => array:2 [
        //         "warning" => "This is a top-10 common password"
        //         "suggestions" => array:1 [
        //         0 => "Add another word or two. Uncommon words are better."
        //         ]
        //     ]
        //     "calc_time" => 0.020488977432251
        // ]
    }
}
```

Play around with different passwords and phrases, the results may surprise you. Check out [Zxcvbn-PHP](https://github.com/bjeavons/zxcvbn-php) for more uses and examples.

### As a validator

The package makes two types of validations available for your application. `zxcvbn` and `zxcvbn_dictionary`.

#### zxcvbn

With this rule you set the lowest score that the phrase need to score wuth Zxcvbn to pass.

**Syntax**

'input' => 'zxcvbn:min_value'

**Example**

``` php
$request->validate([
    'password' => 'zxcvbn:3'
]);
```

You may also initialize the rule as an object:

``` php
use Olssonm\Zxcvbn\Rules\Zxcvbn;

function rules() 
{
    return [
        'password' => ['required', new Zxcvbn($minScore = 3)]
    ];
}
```

In this example the password should at least have a "score" of three (3) to pass the validation. Of course, you should probably use the [zxcvbn-library](https://github.com/dropbox/zxcvbn) on the front-end too to allow the user to know this before posting the form.

#### zxcvbn_dictionary

This is a bit more interesting. `zxcvbn_dictionary` allows you to input both the users username and/or email together with their password (you need suply one piece of user input). The validator checks that the password doesn't exist in the username, or that they are too similar.

**Syntax**

'input' => 'zxcvbn_dictionary:input1,input2'

**Example**

``` php
$request->validate([
    'password' => sprintf('zxcvbn_dictionary:%s,%s', $request->username, $request->email)
]);
```

``` php
use Olssonm\Zxcvbn\Rules\ZxcvbnDictionary;

function rules() 
{
    return [
        'password' => ['required', new ZxcvbnDictionary($this->username)]
    ];
}
```

## Testing

```bash
$ composer test
```

or

```bash
$ phpunit
```

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.

© 2022 [Marcus Olsson](https://marcusolsson.me).

[ico-version]: https://img.shields.io/packagist/v/olssonm/l5-zxcvbn.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/olssonm/l5-zxcvbn.svg?style=flat-square

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[ico-travis]: https://img.shields.io/travis/olssonm/laravel-zxcvbn/master.svg?style=flat-square

[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/olssonm/l5-zxcvbn.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/olssonm/l5-zxcvbn

[link-travis]: https://travis-ci.org/olssonm/laravel-zxcvbn

[link-scrutinizer]: https://scrutinizer-ci.com/g/olssonm/l5-zxcvbn
