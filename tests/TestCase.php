<?php

namespace Olssonm\Zxcvbn\Test;

use Olssonm\Zxcvbn\ZxcvbnServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ZxcvbnServiceProvider::class
        ];
    }
}
