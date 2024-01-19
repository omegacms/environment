<?php
/**
 * Part of Omega CMS - Commands Package
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
use function pcntl_async_signals;
use function pcntl_signal;
use function strtoupper;
use function substr;
use Omega\Helpers\App;
use Omega\Helpers\Alias;
use Omega\Helpers\System;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Serve cli command.
 *
 * The `ServeCommand` starts a development server using PHP's built-in web server.
 *
 * @category    Omega
 * @package     Omega\Commands
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class ServeCommand extends Command
{
    /**
     * Default command name.
     *
     * @var string $defaultName Holds the default command name.
     */
    protected static $defaultName = 'serve';

    /**
     * Process object.
     *
     * @var Process $process Holds an instance of Process.
     */
    private Process $process;

    /**
     * Configures the current command.
     *
     * @return void
     */
     protected function configure() : void
     {
         $this->setDescription( 'Starts a development server' )
              ->setHelp( 'You can provide an optional host and port for the development server.' )
              ->addOption( 'host', null, InputOption::VALUE_REQUIRED, 'The host name or IP address to bind the server to' )
              ->addOption( 'port', null, InputOption::VALUE_REQUIRED, 'The port number to listen on' );
     }

     /**
      * Executes the current command.
      *
      * This method starts a development server using the PHP built-in web server.
      * You can specify the host and port for the server using the '--host' and '--port'
      * options.
      *
      * @param  InputInterface  $input  Holds an instance of InputInterface.
      * @param  OutputInterface $output Holds an instance of OutputInterface.
      * @return int Return 0 if the server started successfully, or an exit code if there was an issue.
      * @throws InvalidArgumentException If 'APP_HOST' or 'APP_PORT' environment variables are missing
      */
    protected function execute( InputInterface $input, OutputInterface $output ) : int
    {
        $base = App::application( 'paths.base' );
        $host = $input->getOption( 'host' ) ?: Alias::env( 'APP_HOST', '127.0.0.1' );
        $port = $input->getOption( 'port' ) ?: Alias::env( 'APP_PORT', '8000' );

        if ( empty( $host ) || empty( $port ) ) {
            throw new InvalidArgumentException(
                'APP_HOST and APP_PORT both need values'
            );
        }

        if ( System::getOperatingSystem() === 'windows' ) {
            $this->startServer( $host, $port, $output );
        } else {
            $this->handleSignals();
            $this->startProcess( $host, $port, $base, $output );
        }

        return Command::SUCCESS;
    }

    private function startServer( string $host, string $port, OutputInterface $output ) : void
    {
        $output->writeln( "Serving requests at http://$host:$port" );

        $serverCommand = "php -S $host:$port -t public";
        $process       = Process::fromShellCommandLine( $serverCommand );

        $process->setTimeout( null );

        $process->run( function ( $type, $buffer ) use ( $output ) {
            $output->write( $buffer );
        } );

        if ( ! $process->isSuccessful() ) {
            $output->writeln( "<error>Server failed to start: " . $process->getExitCodeText() . "</error>" );
        }
    }

    /**
     * Generate the command parameters for starting the PHP built-in web server.
     *
     * @param string $host Holds the host name or IP address to bind the server to.
     * @param string $port Holds the port to use for the server.
     * @param string $base Holds the base path of the application.
     * @return array Return an array of command parameters for starting the server.
     */
    private function command( string $host, string $port, string $base ) : array
    {
        $separator = DIRECTORY_SEPARATOR;

        return [
            PHP_BINARY,
            "-S",
            "{$host}:{$port}",
            "{$base}{$separator}serve.php",
        ];
    }

    /**
     * Set up signal handling to gracefully terminate the PHP built-in server process.
     *
     * This method enables asynchronous signals handling to allow for graceful termination
     * of the PHP built-in server process when needed.
     *
     * @return void
     */
    private function handleSignals() : void
    {
        pcntl_async_signals( true );
        pcntl_signal( SIGTERM, function ( $signal ) {
            if ( $signal === SIGTERM ) {
                $this->process->signal( SIGKILL );
                exit;
            }
        } );
    }

    /**
     * Start the PHP built-in server process.
     *
     * This method starts the PHP built-in server process, handling signals and displaying
     * relevant information in the console output.
     *
     * @param string          $host   Holds the hostname or IP address to bind the server to.
     * @param string          $port   Holds the port number to listen on.
     * @param string          $base   Holds the base path of the server's document root.
     * @param OutputInterface $output Holds the output interface for displaying server information.
     * @return void
     */
    private function startProcess( string $host, string $port, string $base, OutputInterface $output ) : void
    {
        $this->process = new Process( $this->command( $host, $port, $base ), $base );
        $this->process->setTimeout( PHP_INT_MAX );
        $this->process->start( function ( $type, $buffer ) use ( $output ) {
            $output->write( "<info>{$buffer}</info>" );
        } );

        $output->writeln( "Serving requests at http://{$host}:{$port}" );

        $this->process->wait();
    }
}
