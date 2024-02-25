<?php
/**
 * Part of Omega CMS - Environment Package
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
namespace Omega\Environment;

/**
 * @use
 */
use function strtolower;
use function Omega\Helpers\value;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;
use PhpOption\Some;
use RuntimeException;

/**
 * Env class.
 * 
 * The `Env` class is part of the Omega CMS and is designed to handle environment configuration 
 * in a PHP application. It provides methods to safely retrieve environment variable values, 
 * facilitating configuration management in an application.
 * 
 * @category    Omega
 * @package     Omega\Environment
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class Env
{
    /**
     * Indicates if the putenv adapter is enabled.
     *
     * @var bool $putenv True ig the putenv adapter is enabled.
     */
    protected static $putenv = true;

    /**
     * The environment repository instance.
     *
     * @var ?RepositoryInterface $repository Holds the current repository instance or null.
     */
    protected static ?RepositoryInterface $repository;

    /**
     * Enable the putenv adapter.
     *
     * @return void
     */
    public static function enablePutenv() : void
    {
        static::$putenv     = true;
        static::$repository = null;
    }

    /**
     * Disable the putenv adapter.
     *
     * @return void
     */
    public static function disablePutenv() : void
    {
        static::$putenv     = false;
        static::$repository = null;
    }

    /**
     * Gets the repository instance for managing environment variables.
     *
     * @return RepositoryInterface Return the current repository instance.
     */
    public static function getRepository() : RepositoryInterface
    {
        if ( static::$repository === null ) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();

            if ( static::$putenv ) {
                $builder = $builder->addAdapter(PutenvAdapter::class);
            }

            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

    /**
     * Retrieves the value of an environment variable.
     *
     * Takes a key and an optional default value.
     * 
     * @param  string $key     Holds the environment variable key. 
     * @param  mixed  $default Holds the environment variable value or null.
     * @return mixed
     */
    public static function get( string $key, mixed $default = null ) : mixed
    {
        return self::getOption( $key )->getOrCall( fn () => value( $default ) );
    }

    /**
     * Retrieves the value of an environment variable and throws an exception if not defined.
     *
     * @param  string  $key Holds the environment variable key.
     * @return mixed
     * @throws RuntimeException if environment variable has no value.
     */
    public static function getOrFail( string $key ) : mixed
    {
        return self::getOption( $key )->getOrThrow(
            new RuntimeException(
                "Environment variable [$key] has no value."
            )
        );
    }

    /**
     * Gets the possible option for the specified environment variable.
     * 
     * Maps the value returned by the repository, converting special 
     * strings like 'true', 'false', etc.
     *
     * @param  string $key Holds the environment variable key.
     * @return Some Return the current instance of Same.
     */
    protected static function getOption( string $key ) : Some
    {
        return Option::fromValue( static::getRepository()->get( $key ) )
            ->map(function ( $value ) {
                switch ( strtolower( $value ) ) {
                    case 'true':
                    case '(true)':
                        return true;
                    case 'false':
                    case '(false)':
                        return false;
                    case 'empty':
                    case '(empty)':
                        return '';
                    case 'null':
                    case '(null)':
                        return;
                }

                if ( preg_match( '/\A([\'"])(.*)\1\z/', $value, $matches ) ) {
                    return $matches[ 2 ];
                }

                return $value;
            });
    }}
