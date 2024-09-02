![PHPStan Level](https://img.shields.io/badge/PHPStan-level_9-brightgreen)
# Environment Package

The `Environment` package is an integral part of the framework, containing various classes and commands designed to manage different components within the environment. This package provides the necessary tools to interact with and manipulate the environment in which the framework operates.

One of the key commands in this package is the `Serve` command. The Serve command is used to start a local server that allows you to run and test your application in a development environment. However, there are some differences in how this command functions depending on the operating system being used. On `Linux` and `macOS`, the Serve command typically runs natively and utilizes the [`pcntl` (Process Control) libraries](https://www.php.net/manual/en/book.pcntl.php), which are specific to Unix-based systems, to manage server processes effectively. On Windows, however, the command might require additional configurations or behave differently, as Windows does not support pcntl libraries and handles server processes and networking in a different manner.

You can customize the host and port for the local server by modifying the .env file in your project. The relevant environment variables are APP_HOST for the host address and APP_PORT for the port number. Update these variables in the .env file to change the server's configuration.

Another important component within the Environment package is the EnvironmentDetector class. This class plays a crucial role in identifying the environment in which the application is running. It detects the operating system and other environmental factors, and can also determine if the application is running via a web server or through the command line interface (CLI). This capability allows the framework to adjust its behavior and configurations based on whether the application is being accessed through a web request or executed directly from the CLI.

Additionally, the package includes a custom Dotenv class. This class is used to manage environment variables by loading them from a .env file into the application's environment. Dotenv simplifies configuration management, allowing for a clean separation between application code and configuration settings, which is essential for maintaining different environments (development, staging, production) efficiently.

## Requirements

* PHP 8.2 or later

## Installation via Composer

Add `"omegacms/environment": "^1.0.0"` to the require block in your `composer.json` file and then run `composer install`.

```json
{
    "require": {
        "omegacms/environment": "^1.0.0"
    }
}
```

Alternatively, you can simply run the folowing from the command line:

```sh
composer require omegacms/environment "^1.0.0"
```

If you want to include the test sources, use:

```sh
composer require --prefer-source omegacms/environment "^1.0.0"
```

## Customizing the Integrated Server

You can adjust the test server specifications to meet your needs by simply editing the .env file and customizing the following variables:

```
APP_HOST=your_host_here
APP_PORT=your_host_port_here
```

## Documentation

- [Dotenv Documentation](docs/Dotenv.md)

## Contributing

If you'd like to contribute to the OmegaCMS Environment package, please follow our [contribution guidelines](CONTRIBUTING.md).

## License

This project is open-source software licensed under the [GNU General Public License v3.0](LICENSE).
