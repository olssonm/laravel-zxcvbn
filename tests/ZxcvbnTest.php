<?php namespace Olssonm\Zxcvbn\Tests;

use Validator;

use Zxcvbn;

class ZxcvbnTest extends \Orchestra\Testbench\TestCase {

	public function setUp() {
        parent::setUp();
    }

    /**
     * Load the package
     * @return array the packages
     */
    protected function getPackageProviders($app)
    {
        return [
            'Olssonm\Zxcvbn\ZxcvbnServiceProvider'
        ];
    }

	/**
     * Load the alias
     * @return array the aliases
     */
	protected function getPackageAliases($app)
	{
		return [
            'Zxcvbn' => 'Olssonm\Zxcvbn\Facades\Zxcvbn'
        ];
	}

    /**
     * Just run som standard tests to see that Zxcvbn is up to snuff and working
     * @test
     */
	public function test_zxcvbn_basics()
    {
		$testVar1 = Zxcvbn::passwordStrength('test');

		// Check keys
		$this->assertArrayHasKey('score', $testVar1);
		$this->assertArrayHasKey('match_sequence', $testVar1);
		$this->assertArrayHasKey('entropy', $testVar1);
		$this->assertArrayHasKey('password', $testVar1);
		$this->assertArrayHasKey('calc_time', $testVar1);
		$this->assertArrayHasKey('crack_time', $testVar1);

		// Check score-value
		$this->assertEquals(0, $testVar1['score']);

		// Run some more tests
		$testVar2 = Zxcvbn::passwordStrength('dadaurka');
		$testVar3 = Zxcvbn::passwordStrength('staple horse battery');
		$testVar4 = Zxcvbn::passwordStrength('7E6k9axB*gwGHa&aZTohmD9Wr&NVs[b4'); //<-- 32

		// Check score-value
		$this->assertEquals(1, $testVar2['score']);
		$this->assertEquals(4, $testVar3['score']);
		$this->assertEquals(4, $testVar4['score']);
    }

	/** @test */
	public function test_password_strength()
	{
		// Standard tests
		$this->assertEquals(true, $this->validate_without_message_min('test', 0));
		$this->assertEquals(false, $this->validate_without_message_min('test', 4));

		$this->assertEquals(true, $this->validate_without_message_min('staple horse battery', 3));
		$this->assertEquals(true, $this->validate_without_message_min('staple horse battery', 4));
		$this->assertEquals(false, $this->validate_without_message_min('staple horse battery', 5));
	}

	/** @test */
	public function test_password_strength_with_message()
	{
		// Standard message
		$this->assertEquals('Your password is not secure enough.', $this->validate_with_message_min('staple horse battery', 5, null));
		$this->assertEquals('Just a message', $this->validate_with_message_min('test', 4, 'Just a message'));
	}

	/** @test */
	public function test_password_dictionary()
	{
		// Standard tests
		$this->assertEquals(false, $this->validate_without_message_dictionary('password', 'test@test.com', 'test'));
		$this->assertEquals(false, $this->validate_without_message_dictionary('test', 'test@test.com', 'test'));
		$this->assertEquals(false, $this->validate_without_message_dictionary('721ahsa!', '721ahsa@test.com', '721ahsa'));

		$this->assertEquals(true, $this->validate_without_message_dictionary('721ahsa!', 'dadaurka@test.com', 'dadaurka'));
		$this->assertEquals(true, $this->validate_without_message_dictionary('asd912j!', 'myemail@test.com', 'username'));
		$this->assertEquals(true, $this->validate_without_message_dictionary('asd912j!', 'trash@thedumpster.com', 'username'));

		$this->assertEquals(true, $this->validate_without_message_dictionary('asd912j!', null, 'username'));
		$this->assertEquals(true, $this->validate_without_message_dictionary('asd912j!', null, null));
	}

	/** @test */
	public function test_password_dictionary_with_message()
	{
		// Standard message
		$this->assertEquals('Your password is insecure. It either matches a commonly used password, or you have used a similar username/password combination.', $this->validate_with_message_dictionary('password', 'test@test.com', 'test', null));
		$this->assertEquals('Just a message', $this->validate_with_message_dictionary('test', 'test@test.com', 'test', 'Just a message'));
	}

	private function validate_without_message_min($password, $min)
	{
		$data = ['password' => $password];
        $validator = Validator::make($data, [
            'password' => 'zxcvbn_min:' . $min . '|required',
        ]);

        return $validator->passes();
	}

	private function validate_with_message_min($password, $min, $message)
	{
		$data = ['password' => $password];
        $validator = Validator::make($data, [
            'password' => 'zxcvbn_min:' . $min . '|required',
        ], [
			'password.zxcvbn_min' => $message
		]);

		$errors = $validator->errors();
        return $errors->first('password');
	}

	private function validate_without_message_dictionary($password, $email, $username)
	{
		$data = ['password' => $password];
        $validator = Validator::make($data, [
            'password' => 'zxcvbn_dictionary:' . $username . ',' . $email . '|required',
        ]);

        return $validator->passes();
	}

	private function validate_with_message_dictionary($password, $email, $username, $message)
	{
		$data = ['password' => $password];
        $validator = Validator::make($data, [
            'password' => 'zxcvbn_dictionary:' . $username . ',' . $email . '|required',
        ], [
			'password.zxcvbn_dictionary' => $message
		]);

		$errors = $validator->errors();
        return $errors->first('password');
	}
}
