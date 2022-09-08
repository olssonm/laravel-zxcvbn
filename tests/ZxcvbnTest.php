<?php

namespace Olssonm\Zxcvbn\Test;

use Illuminate\Support\Facades\Validator;
use Olssonm\Zxcvbn\Facades\Zxcvbn;
use Olssonm\Zxcvbn\Rules\Zxcvbn as ZxcvbnRule;
use Olssonm\Zxcvbn\Rules\ZxcvbnDictionary as ZxcvbnDictionaryRule;
use Olssonm\Zxcvbn\ZxcvbnServiceProvider;
use ZxcvbnPhp\Zxcvbn as ZxcvbnPhp;

it('loads package', function () {
    $providers = $this->app->getLoadedProviders();
    $this->assertTrue(array_key_exists(ZxcvbnServiceProvider::class, $providers));
});

it('loads facade', function () {
    $facade = $this->app['zxcvbn'];
    $this->assertTrue(is_a($facade, ZxcvbnPhp::class));
});

it('can perform zxcvbn basics', function () {
    $zxcvbn = Zxcvbn::passwordStrength('password');

    $testVar1 = Zxcvbn::passwordStrength('test');

    // Check keys
    $this->assertArrayHasKey('score', $testVar1);
    $this->assertArrayHasKey('sequence', $testVar1);
    $this->assertArrayHasKey('crack_times_seconds', $testVar1);
    $this->assertArrayHasKey('crack_times_display', $testVar1);
    $this->assertArrayHasKey('calc_time', $testVar1);
    $this->assertArrayHasKey('guesses', $testVar1);

    // Check score-value
    $this->assertEquals(0, $testVar1['score']);

    // Run some more tests
    $testVar2 = Zxcvbn::passwordStrength('dadaurka');
    $testVar3 = Zxcvbn::passwordStrength('staple horse battery');
    $testVar4 = Zxcvbn::passwordStrength('7E6k9axB*gwGHa&aZTohmD9Wr&NVs[b4'); //<-- 32

    // Check score-value
    $this->assertEquals(2, $testVar2['score']);
    $this->assertEquals(4, $testVar3['score']);
    $this->assertEquals(4, $testVar4['score']);
});

it('can validate min-rule', function () {
    // Fails: returns message
    $this->assertEquals('Just a test message', min_validation('test', 4, 'Just a test message'));
    $this->assertEquals('Just another test message', min_validation('test', 4, 'Just another test message'));
    $this->assertEquals('The password is not strong enough.', min_validation('staple horse battery', 5, null));

    // Passes: returns true
    $this->assertEquals(true, min_validation('test', 0));
    $this->assertEquals(true, min_validation('staple horse battery', 3));
    $this->assertEquals(true, min_validation('staple horse battery', 4));
});

it('can validate dictionary-rule', function () {
    // Fails: returns message
    $this->assertEquals('The password is too simililar to another field.', dictionary_validation('dadaurka', 'test@test.com', 'dadaurka', null));
    $this->assertEquals('The password is too simililar to another field.', dictionary_validation('dadaurka', 'dadaurka', null, null));
    $this->assertEquals('Just a message', dictionary_validation('test', 'test@test.com', 'test', 'Just a message'));

    // Passes: returns true
    $this->assertEquals(true, dictionary_validation('d5=:r+AEl5?+', 'dadaurka@test.com', 'dadaurka', null));
    $this->assertEquals(true, dictionary_validation('Mo]R^v@vYo]I', 'myemail@test.com', 'username', null));
    $this->assertEquals(true, dictionary_validation('%!/%^Qz1q&KH', 'trash@thedumpster.com', 'username', null));
    $this->assertEquals(true, dictionary_validation('O`l}/RqR9$.S','trash@thedumpster.com', null, null));
});

it('can validate rules as objects', function() {
    // Pass min-rule, fail dictionary-rule
    $this->assertEquals('The password is too simililar to another field.', rule_validator(3, 'gagadododaka', 'gagadododaka@test.com', 'gagadododaka', null));

    // Fail min-rule, pass dictionary-rule
    $this->assertEquals('The password is not strong enough.', rule_validator(4, 'test', 'trash@thedumpster.com', 'username', null));

    // Pass both rules
    $this->assertEquals('The password is not strong enough.', rule_validator(7, 'O`l}/RqR9$.S', 'trash@thedumpster.com', null));
});

/** @note validation helper */
function min_validation($password, $min, $message = null)
{
    $data = ['password' => $password];
    $validator = Validator::make($data, [
        'password' => ['required', 'zxcvbn:' . $min],
    ], $message ? ['password.zxcvbn' => $message] : []);

    if (!$validator->passes()) {
        $errors = $validator->errors('password');
        return $errors->first('password');
    }

    return $validator->passes();
}

/** @note validation helper */
function dictionary_validation($password, $email, $username, $message = null)
{
    $data = ['password' => $password];
    $validator = Validator::make($data, [
        'password' => 'zxcvbn_dictionary:' . $username . ',' . $email . '|required',
    ], $message ? ['password.zxcvbn_dictionary' => $message] : []);

    if (!$validator->passes()) {
        $errors = $validator->errors('password');
        return $errors->first('password');
    }

    return $validator->passes();
}

/** @note object rule validator  */
function rule_validator($min, $password, $email, $username, $message = null)
{
    $data = ['password' => $password];
    $validator = Validator::make($data, [
        'password' => ['required', new ZxcvbnDictionaryRule($username, $email), new ZxcvbnRule($min)],
    ], $message ? ['password.zxcvbn' => $message] : []);

    if (!$validator->passes()) {
        $errors = $validator->errors('password');
        return $errors->first('password');
    }

    return $validator->passes();
}
