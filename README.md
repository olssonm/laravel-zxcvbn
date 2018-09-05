# Zxcvbn for Laravel 5

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total downloads][ico-downloads]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Scrutinizer Score][ico-scrutinizer]][link-scrutinizer]

![zxcvbn](https://user-images.githubusercontent.com/907114/41193108-747d9b50-6c08-11e8-8f9c-57874f52fa9b.png)

A simple implementation of zxcvbn for Laravel 5. This package allows you to access "zxcvbn-related" data on a passphrase in the application and also to use zxcvbn as a standard validator.

Uses [Zxcvbn-PHP](https://github.com/bjeavons/zxcvbn-php) by [@bjeavons](https://github.com/bjeavons), which in turn is inspired by [zxcvbn](https://github.com/dropbox/zxcvbn) by [@dropbox](https://github.com/dropbox).

## Install

Via Composer

```bash
$ composer require olssonm/l5-zxcvbn
```

Add the package to your providers array (will be added automatically in Laravel 5.5+):

```php
'providers' => [
    Olssonm\Zxcvbn\ZxcvbnServiceProvider::class,
]
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

```php
<?php

use Zxcvbn;

class MyClass extends MyOtherClass
{
    public function myFunction()
    {
        $zxcvbn = Zxcvbn::passwordStrength('password');
        dd($zxcvbn);

        // array:6 [▼
        //   "crack_time" => 5.0E-5
        //   "calc_time" => 0.12961101531982
        //   "password" => "password"
        //   "entropy" => 0.0
        //   "match_sequence" => array:1 []
        //   "score" => 0
        // ]
    }
}
?>
```

Play around with different passwords and phrases, the results may surprise you. Check out [Zxcvbn-PHP](https://github.com/bjeavons/zxcvbn-php) for more uses and examples.

### As a validator

The package gives you two different validation rules that you may use; `zxcvbn_min` and `zxcvbn_dictionary`.

#### zxcvbn_min

`zxcvbn_min` allows you to set up a rule for minimum score that the value beeing tested should adhere to.

**Syntax**

    input' => 'zxcvbn_min:min_value'

**Example**

```php
<?php
    $data = ['password' => 'password'];
    $validator = Validator::make($data, [
        'password' => 'zxcvbn_min:3|required',
    ], [
        'password.zxcvbn_min' => 'Your password is not strong enough!'
    ]);
```

In this example the password should at least have a "score" of three (3) to pass the validation. Of course, you should probably use the zxcvbn-library on the front-end too to allow the user to know this before posting the form...

#### zxcvbn_dictionary

This is a bit more interesting. `zxcvbn_dictionary` allows you to input both the users username and/or email, and their password. The validator checks that the password doesn't exist in the username, or that they are too similar.

**Syntax**

    'input' => 'xcvbn_dictionary:username,email'

**Example**

```php
<?php
    /**
     * Example 1, pass
     */
    $password = '31??2sa//"dhjd2askjd19sad19!!&!#"';
    $data = [
        'username'  => 'user',
        'email'     => 'trash@thedumpster.com'
    ];
    $validator = Validator::make($password, [
        'password' => 'zxcvbn_dictionary:' . $data['username'] . ',' . $data['email'] . '|required',
    ]);

    dd($validator->passes());
    // true

    /**
     * Example 2, fail
     */
    $password = 'mycomplicatedphrase';
    $data = [
        'username'  => 'mycomplicatedphrase',
        'email'     => 'mycomplicatedphrase@thedumpster.com'
    ];
    $validator = Validator::make($password, [
        'password' => 'zxcvbn_dictionary:' . $data['username'] . ',' . $data['email'] . '|required',
    ]);

    dd($validator->passes());
    // false
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

© 2018 [Marcus Olsson](https://marcusolsson.me).

[ico-version]: https://img.shields.io/packagist/v/olssonm/l5-zxcvbn.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/olssonm/l5-zxcvbn.svg?style=flat-square

[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[ico-travis]: https://img.shields.io/travis/olssonm/l5-zxcvbn/master.svg?style=flat-square

[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/olssonm/l5-zxcvbn.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/olssonm/l5-zxcvbn

[link-travis]: https://travis-ci.org/olssonm/l5-zxcvbn

[link-scrutinizer]: https://scrutinizer-ci.com/g/olssonm/l5-zxcvbn
