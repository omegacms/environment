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
namespace Omega\Environment\ServiceProvider;

/**
 * @use
 */
use Omega\Application\Application;
use Omega\Environment\Environmentsssssss;

/**
 * Environment service provider class.
 * 
 * The `EnvironmentServiceProvider` class provides a service binding for the `Environment` class
 * within the Omega framework. It allows you to easily access environment parameters throughout 
 * your application.
 *
 * @category    Omega
 * @package     Omega\Environment
 * @subpackage  Omega\Environment\ServiceProvider
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class EnvironmentServiceProvider
{
    /**
     * Bind the environment.
     * 
     * Binds an instance of the `Environment` class to the application container, allowing you
     * to resolve it using the `environment` key.
     *
     * @param  Application $application Holds an instance of Application.
     * @return void
     */
    public function bind( Application $application ) : void
    {
        $application->bind( 'environment', function() {
            return new Environment();
        } );
    }
}