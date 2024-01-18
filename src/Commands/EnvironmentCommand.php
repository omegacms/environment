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
namespace Omega\Environment\Commands;

/**
 * @use
 */
use Omega\Helpers\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * ConfigureEnvironmentCommand class.
 *
 * The `EnvironmentCommand` is responsible for configuring the environment file.
 *
 * @category    Omega
 * @package     Omega\Commands
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class EnvironmentCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure() : void
    {
        $this->setName( 'configure:env' )
            ->setDescription( 'Configure the environment file.' )
            ->setHelp( 'This command allows you to configure the environment file for your application.' );
    }

    /**
     * Executes the current command.
     *
     * This method is called when the command is executed.
     *
     * @param  InputInterface  $input Holds an instance of InputInterface.
     * @param  OutputInterface $output Holds an instance of OutputInterface.
     * @return int Return 0 if the command executed successfully, or an error code.
     */
    protected function execute( InputInterface $input, OutputInterface $output ) : int
    {
        // Create a Filesystem instance
        $filesystem = new Filesystem();

        // Path to the environment stub file
        $stubFilePath = __DIR__ . '/stubs/env.stub';

        // Path to the environment file in the root of the project
        //$envFilePath = $this->getApplication()->getBasePath() . '/.env';
        $envFilePath = getcwd() . '/.env';

        // Check if file .env exists. If true the application is configured.
        // False if not.
        if ( ! file_exists( $envFilePath ) ) {
            // Ask the user to choose the environment
            $environment = $this->askEnvironment( $input, $output );

            // Update the environment file based on the operating system
            if ( System::getOperatingSystem() === 'windows' ) {
                $this->updateEnvironmentFileWindows( $environment, $filesystem, $stubFilePath, $envFilePath );
            } else {
                $this->updateEnvironmentFileLinux( $environment, $filesystem, $stubFilePath, $envFilePath );
            }

            // Output a success message
            $output->writeln( '<info>Environment configuration completed successfully.</info>' );
        }

        return Command::SUCCESS;
    }

    /**
     * Ask the user to choose the environment.
     *
     * @param  InputInterface  $input  Holds an instance of InputInterface.
     * @param  OutputInterface $output Holds an instance of OutputInterface.
     * @return string Return the chosen environment.
     */
    private function askEnvironment( InputInterface $input, OutputInterface $output ) : string
    {
        // Define environment choices
        $choices = [
            'Development (dev)',
            'Production (prod)',
            'Staging (stag)',
        ];

        // Create a ChoiceQuestion to ask the user
        $question = new ChoiceQuestion( 'Choose the environment:', $choices );

        // Ask the user and get the answer
        $environment = $this->getHelper( 'question' )->ask( $input, $output, $question );

        // Map the user's choice to the actual environment value
        $environmentMap = [
            'Development (dev)' => 'dev',
            'Production (prod)' => 'prod',
            'Staging (stag)'    => 'stag',
        ];

        return $environmentMap[ $environment ];
    }

    /**
     * Update the environment file for Windows.
     *
     * @param  string     $environment  Holds the chosen environment.
     * @param  Filesystem $filesystem   Holds an instance of Filesystem.
     * @param  string     $stubFilePath Holds the path to the environment stub file.
     * @param  string     $envFilePath  Holds the path to the environment file.
     * @return void
     */
    private function updateEnvironmentFileWindows( string $environment, Filesystem $filesystem, string $stubFilePath, string $envFilePath ) : void
    {
        $this->updateEnvironmentFile( $environment, $filesystem, $stubFilePath, $envFilePath );
    }

    /**
     * Update the environment file for Linux.
     *
     * @param  string     $environment  Holds the chosen environment.
     * @param  Filesystem $filesystem   Holds an instance of Filesystem.
     * @param  string     $stubFilePath Holds the path to the environment stub file.
     * @param  string     $envFilePath  Holds the path to the environment file.
     * @return void
     */
    private function updateEnvironmentFileLinux( string $environment, Filesystem $filesystem, string $stubFilePath, string $envFilePath ) : void
    {
        $this->updateEnvironmentFile( $environment, $filesystem, $stubFilePath, $envFilePath );
    }

    /**
     * Update the environment file.
     *
     * @param  string     $environment  Holds the chosen environment.
     * @param  Filesystem $filesystem   Holds an instance of Filesystem.
     * @param  string     $stubFilePath Holds the path to the environment stub file.
     * @param  string     $envFilePath  Holds the path to the environment file.
     * @return void
     */
    private function updateEnvironmentFile( string $environment, Filesystem $filesystem, string $stubFilePath, string $envFilePath ) : void
    {
        // Get the content of the stub file
        $stubContent = file_get_contents( $stubFilePath );

        // Compile the stub with the chosen environment
        $compiledContent = str_replace( 'APP_ENV=', "APP_ENV=$environment", $stubContent );

        // Write the compiled file to the root of the project
        $filesystem->dumpFile( $envFilePath, $compiledContent );
    }
}
