<?php

namespace Omega\Environment\Tests;

use Omega\Environment\Dotenv;
use PHPUnit\Framework\TestCase;

class DotenvTest extends TestCase
{
    public function tearDown() : void
    {
        Dotenv::flush();
        Dotenv::setRequired([]);
    }

    public function test_it_can_load_env_file_and_gives_access_to_vars_using_all()
    {
        Dotenv::flush();
        
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        $expected = [
            'DB_USER'         => 'root',
            'DB_PASSWORD'     => 'secret',
            'DB_NAME'         => 'test',
            'TEST_USER'       => 'root',
            'TEST_SOME_ARRAY' => 'FOO'
        ];

        $this->assertSame($expected, Dotenv::all());
    }

    public function test_it_can_load_array() 
    {
        Dotenv::load(__DIR__ . '/fixtures', '.env.test');
    
        $loadedVariables = Dotenv::all();
    
        $expected = [
            'DB_USER'         => 'root',
            'DB_PASSWORD'     => 'secret',
            'DB_NAME'         => 'test',
            'TEST_USER'       => 'root',
            'TEST_SOME_ARRAY' => 'FOO',
        ];
    
        $this->assertSame($expected, $loadedVariables);
    }

    public function test_flush_method()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test');
        Dotenv::flush();

        $this->assertSame([], Dotenv::all());
    }

    public function test_get_method()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test');

        Dotenv::set([
            'DB_PASSWORD' => null,
        ]);

        $this->assertSame('root', Dotenv::get('DB_USER'));
        $this->assertSame('root', Dotenv::get('DB_USER', 'foo'));
        $this->assertSame(null, Dotenv::get('DB_PASSWORD'));
        $this->assertSame('foo', Dotenv::get('DB_PASSWORD', 'foo'));
    }

    public function test_set_method_with_two_args()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test');

        Dotenv::set('DB_PASSWORD', 'secret');
        $this->assertSame('root', Dotenv::get('DB_USER'));
        $this->assertSame('secret', Dotenv::get('DB_PASSWORD'));
    }

    public function test_set_method_with_array()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test');

        Dotenv::set([
            'DB_PASSWORD' => 'secret',
            'DB_NAME'     => 'test',
        ]);

        $this->assertSame('root', Dotenv::get('DB_USER'));
        $this->assertSame('secret', Dotenv::get('DB_PASSWORD'));
        $this->assertSame('test', Dotenv::get('DB_NAME'));
    }

    public function test_it_throws_missing_var_exception()
    {
        $this->expectException('\Omega\Environment\Exception\MissingVariableException');

        Dotenv::setRequired(['DB_HOST', 'DB_TYPE']);
        Dotenv::load( __DIR__ . '/fixtures', '.env.test');
    }

    public function test_it_throws_missing_var_exception_even_after_load()
    {
        $this->expectException('\Omega\Environment\Exception\MissingVariableException');

        Dotenv::load( __DIR__ . '/fixtures', '.env.test');

        Dotenv::setRequired(['DB_HOST', 'DB_TYPE']);
    }

    public function test_it_does_not_throw_missing_var_exception_if_all_required_vars_are_set()
    {
        Dotenv::setRequired(['DB_USER', 'DB_PASSWORD']);
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        $this->assertArrayHasKey('DB_USER', Dotenv::all());
        $this->assertArrayHasKey('DB_PASSWORD', Dotenv::all());
    }

    public function test_it_can_copy_vars_to_putenv()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::copyVarsToPutenv();

        $this->assertSame('root', getenv('PHP_TEST_USER'));
        $this->assertSame('FOO', getenv('PHP_TEST_SOME_ARRAY'));
    }

    public function test_it_can_copy_vars_to_env()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::copyVarsToEnv();

        $this->assertSame('root', $_ENV['TEST_USER']);
        $this->assertSame('FOO', $_ENV['TEST_SOME_ARRAY']);
        unset($_ENV['TEST_USER'], $_ENV['TEST_SOME_ARRAY']);
    }

    public function test_it_can_copy_vars_to_server()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::copyVarsToServer();

        $this->assertSame('root', $_SERVER['TEST_USER']);
        $this->assertSame('FOO', $_SERVER['TEST_SOME_ARRAY']);
        unset($_SERVER['TEST_USER'], $_SERVER['TEST_SOME_ARRAY']);
    }
}
