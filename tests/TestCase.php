<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/** Provides the application test foundation. */
abstract class TestCase extends BaseTestCase
{
    /** Boots every test against an isolated in-memory database. */
    public function createApplication(): Application
    {
        $environment = [
            'APP_ENV' => 'testing',
            'APP_KEY' => 'base64:2KJqmObHk3h8+Uj4U8I4uWyM4jZd3zcUVhI2IzZzGUE=',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'CACHE_STORE' => 'array',
            'SESSION_DRIVER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
        ];

        foreach ($environment as $name => $value) {
            putenv($name.'='.$value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }

        $application = require dirname(__DIR__).'/bootstrap/app.php';
        $application->make(Kernel::class)->bootstrap();

        return $application;
    }
}
