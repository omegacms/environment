<?php
/**
 * Part of Omega CMS -  Environment Test Package
 *
 * @link       https://omegacms.github.io
 * @author     Adriano Giovannini <omegacms@outlook.com>
 * @copyright  Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */

/**
 * @declare
 */
declare( strict_types = 1 );

/**
 * @namespace
 */
namespace Omega\Environment\Tests;

/**
 * @use
 */
use Omega\Environment\Dotenv;
use PHPUnit\Framework\TestCase;

/**
 * Dotenv test class.
 * 
 * The `DotenvTest` class extends the `PHPUnit\Framework\TestCase` class and tests 
 * functionalities of the `Omega\Environment\Dotenv` class. It likely uses a `.env.test` 
 * file located in the fixtures directory for testing purposes.
 *
 * @category    Omega
 * @package     Omega\Environment
 * @subpackage  Omega\Environment\Tests
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class DotenvTest extends TestCase
{
    /**
     * Tear down.
     * 
     * This method is executed after each test case. It clears any loaded environment 
     * variables using `Dotenv::flush()` and resets required environment variables to 
     * an empty array using `Dotenv::setRequired( [] )`.
     * 
     * @return void
     */
    public function tearDown() : void
    {
        Dotenv::flush();

        Dotenv::setRequired([]);
    }

    /**
     * Test it can load env file and give access to var using all.
     * 
     * This test simulates loading the .env.test file and asserts that the loaded 
     * variables can be accessed using the `all()` method. It also verifies that the 
     * loaded variables match the expected values.
     * 
     * @return void
     */
    public function testItCanLoadEnvFileAndGivesAccessToVarsUsingAll()
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

        $this->assertSame( $expected, Dotenv::all() );
    }

    /**
     * Test it can load array.
     * 
     * Similar to the previous test, this test loads the `.env.test` file and asserts that 
     * the loaded variables accessible through `all()` match the expected values.
     * 
     * @return void
     */
    public function testItCanLoadArray() 
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );
    
        $loadedVariables = Dotenv::all();
    
        $expected = [
            'DB_USER'         => 'root',
            'DB_PASSWORD'     => 'secret',
            'DB_NAME'         => 'test',
            'TEST_USER'       => 'root',
            'TEST_SOME_ARRAY' => 'FOO',
        ];
    
        $this->assertSame( $expected, $loadedVariables );
    }

    /**
     * Test flush method.
     * 
     * This test loads the `.env.test` file and then uses the `flush()` method to clear 
     * the loaded variables. Finally, it asserts that `all()` returns an empty array 
     * after flushing.
     * 
     * @return void
     */
    public function testFlushMethod()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::flush();

        $this->assertSame( [], Dotenv::all() );
    }

    /**
     * test get method.
     * 
     * This test loads the .env.test file and tests the behavior of the get() method in various scenarios:
     * 
     * * Retrieving an existing variable (`DB_USER`).
     * * Retrieving an existing variable with a default value (`DB_USER`, `foo`).
     * * Retrieving a non-existent variable (`DB_PASSWORD`).
     * * Retrieving a non-existent variable with a default value (`DB_PASSWORD`, `foo`).
     */
    public function testGetMethod()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::set( [
            'DB_PASSWORD' => null,
        ] );

        $this->assertSame( 'root', Dotenv::get( 'DB_USER'            ) );
        $this->assertSame( 'root', Dotenv::get( 'DB_USER',     'foo' ) );
        $this->assertSame(   null, Dotenv::get( 'DB_PASSWORD'        ) );
        $this->assertSame(  'foo', Dotenv::get( 'DB_PASSWORD', 'foo' ) );
    }

    /**
     * Test set method with two array.
     * 
     * This test loads the `.env.test` file and then uses the `set()` method with two 
     * arguments to update the value of a specific variable (`DB_PASSWORD`). It then 
     * asserts that the updated value is accessible through `get()`.
     * 
     * @return void
     */
    public function testSetMethodWithTwoArgs()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::set( 'DB_PASSWORD', 'secret' );
        
        $this->assertSame( 'root',   Dotenv::get( 'DB_USER'     ) );
        $this->assertSame( 'secret', Dotenv::get( 'DB_PASSWORD' ) );
    }

    /**
     * Test set method with array.
     * 
     * This test loads the .env.test file and then uses the `set()` method with 
     * an array to update the values of multiple variables. It asserts that the 
     * updated values are accessible through `get()`.
     * 
     * @return void
     */
    public function testSetMethodWithArray()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::set( [
            'DB_PASSWORD' => 'secret',
            'DB_NAME'     => 'test',
        ] );

        $this->assertSame(   'root', Dotenv::get( 'DB_USER'     ) );
        $this->assertSame( 'secret', Dotenv::get( 'DB_PASSWORD' ) );
        $this->assertSame(   'test', Dotenv::get( 'DB_NAME'     ) );
    }

    /**
     * Test it throws missing var exception.
     * 
     * This test tests the behavior when required environment variables are not defined in 
     * the loaded `.env.test` file. It sets required variables (`DB_HOST`, `DB_TYPE`) and 
     * expects a `MissingVariableException` to be thrown during loading.
     * 
     * @return void
     */
    public function testItThrowsMissingVarException()
    {
        $this->expectException( '\Omega\Environment\Exception\MissingVariableException' );

        Dotenv::setRequired( [ 
            'DB_HOST', 
            'DB_TYPE' 
        ] );
        
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' ) ;
    }

    /**
     * Test it throws is missing var exception even after load.
     *  
     * Similar to the previous test, this test loads the `.env.test` file and then sets required 
     * variables (`DB_HOST`, `DB_TYPE`). It expects a `MissingVariableException` to be thrown 
     * regardless of the prior loading.
     * 
     * @return void
     */
    public function testItThrowIsMissingVarExceptionEvenAfterLoad()
    {
        $this->expectException( '\Omega\Environment\Exception\MissingVariableException' );

        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::setRequired( [
            'DB_HOST', 
            'DB_TYPE'
        ] );
    }

    /**
     * Test it does not throw missing var exception if all required vars are set.
     * 
     * This test sets required variables (`DB_USER`, `DB_PASSWORD`) and then loads the `.env.test` file. 
     * It verifies that the loading is successful and the required variables are accessible through `all()`.
     * 
     * @return void
     */
    public function testItDoesNotThrowMissingVarExceptionIfAllRequiredVarsAreSet()
    {
        Dotenv::setRequired( [
            'DB_USER', 
            'DB_PASSWORD'
        ] );
        
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        $this->assertArrayHasKey( 'DB_USER', Dotenv::all() );
        $this->assertArrayHasKey( 'DB_PASSWORD', Dotenv::all() );
    }

    /**
     * Test it can copy vars to putenv.
     * 
     * This test loads the `.env.test` file and then uses `copyVarsToPutenv()` to 
     * copy the loaded variables to the putenv function. It asserts that the copied 
     * variables are accessible through the `getenv()` function.
     * 
     * @return void 
     */    
    public function testItCanCopyVarsToPutenv()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::copyVarsToPutenv();

        $this->assertSame( 'root', getenv( 'PHP_TEST_USER'       ) );
        $this->assertSame(  'FOO', getenv( 'PHP_TEST_SOME_ARRAY' ) );
    }

    /**
     * Test it can copy vars to env.
     * 
     * This test loads the `.env.test` file and then uses `copyVarsToEnv()` to copy 
     * the loaded variables to the `$_ENV` superglobal. It asserts that the copied 
     * variables are accessible through `$_ENV`.
     * 
     * @return void
     */
    public function testItCanCopyVarsToEnv()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::copyVarsToEnv();

        $this->assertSame( 'root', $_ENV[ 'TEST_USER'       ] );
        $this->assertSame(  'FOO', $_ENV[ 'TEST_SOME_ARRAY' ] );

        unset( $_ENV[ 'TEST_USER' ], $_ENV[ 'TEST_SOME_ARRAY' ] );
    }

    /**
     * Test it can copy vars to server.
     * 
     * This test loads the `.env.test` file and then uses `copyVarsToServer()` to copy the 
     * loaded variables to the `$_SERVER` superglobal. It asserts that the copied variables 
     * are accessible through `$_SERVER`.
     * 
     * @return void
     */
    public function testItCanCopyVarsToServer()
    {
        Dotenv::load( __DIR__ . '/fixtures', '.env.test' );

        Dotenv::copyVarsToServer();

        $this->assertSame( 'root', $_SERVER[ 'TEST_USER'       ] );
        $this->assertSame(  'FOO', $_SERVER[ 'TEST_SOME_ARRAY' ] );
        
        unset( $_SERVER[ 'TEST_USER' ], $_SERVER ['TEST_SOME_ARRAY' ] );
    }
}