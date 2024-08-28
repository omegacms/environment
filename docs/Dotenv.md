# OmegaCMS Environment (Dotenv readme)

## Introduction

The `Dotenv` package is a lightweight utility for managing environment variables in PHP applications. It provides a simple and efficient way to load and access environment-specific configuration from a `.env` file. Unlike some well-known dotenv packages that are primarily designed for development environments, this package is tailored for production use, ensuring both speed and security by avoiding direct manipulation of `$_ENV` or `$_SERVER` by default.

**Note:** The `Dotenv` class is a component of the larger `environment` package. It is not a standalone package but rather an integrated part of a broader environment management system. This means that while `Dotenv` handles environment variable management, it works in conjunction with other components of the `environment` package to offer a cohesive configuration management experience.

With its focus on simplicity and efficiency, `Dotenv` is ideal for scenarios where performance and security are paramount, and it avoids some of the complexities associated with more feature-rich dotenv solutions.


## Installation

1. `composer require omega/environment`

2. Create a `.env` file to store configuration settings that are environment-specific or sensitive.

   Example:

```php
APP_ENV=dev 
APP_HOST=127.0.0.1 
APP_PORT=8000
```


This file should NEVER be added to version control.

3. Create a `.env.example` file and add it to version control. This file should serve as an example for developers of how the `.env` file should look.

4. Load the `.env` file:

```php
$dotenv = new Dotenv();
// Pass the directory path containing the .env file

$dotenv->load('/path/to/your/project'); 
```

Alternatively, if you are using a framework that manages paths for you, you can pass a pointer to the path. For example, if the variable `$basePath` contains the path to the root of your project or the location where the .env file resides (`/your_directory/yuour_project`), you can pass `$dotenv->load($basePath);`.

## Usage

### Getting Data

To retrieve an environment variable using the static get method:

```php
$dbUser = Dotenv::get('DB_USER');
```

You can pass a second parameter to be used as the default value if the variable is not set:

```php
$dbUser = Dotenv::get('DB_USER', 'admin');
```

> Note: There is no need to instantiate the Dotenv class to access its methods, as all the methods are static. This makes accessing environment variables straightforward and efficient.

To avoid importing the Dotenv class in every file, you can create a global helper function:

```php
function env($key, $default = null)
{
    return Dotenv::get($key, $default);
}

// Example usage
$dbUser = env('DB_USER', 'admin');
```

You can also retrieve all environment variables at once:

```php
$variables = Dotenv::all();
```

### Setting Data
You can set or override specific environment variables using the static set method:

```php
Dotenv::set('DB_USER', 'admin');
Dotenv::set('DB_PASSWORD', 'secret');
// or
Dotenv::set([
    'DB_USER'     => 'root',
    'DB_PASSWORD' => 'secret',
]);
```

If you want to reload all environment variables entirely from a file or an array:

```php
DotEnv::load('/path/to/new/project');
// or
DotEnv::load([
    'DB_USER'     => 'root',
    'DB_PASSWORD' => 'secret',
]);
```

### Checking Required Variables

You can ensure that specific environment variables are present using the static setRequired method:

```php
Dotenv::setRequired(['DB_USER', 'DB_PASSWORD']);
```

If any of the required variables are not loaded, a MissingVariableException will be thrown.

### Copying Variables
Although it's generally not necessary, you can copy all environment variables to putenv(), $_ENV, or $_SERVER using the following static methods:

```php
Dotenv::copyVarsToPutenv($prefix = 'PHP_'); // putenv()
Dotenv::copyVarsToEnv(); // $_ENV
Dotenv::copyVarsToServer(); // $_SERVER
```
This structure clarifies the usage of static methods, emphasizing that there's no need to instantiate the Dotenv class, making it easier to access environment variables efficiently.

### Testing

Q: Why are there so many static calls? How am I supposed to mock them in tests?

A: You shouldn't mock the `Dotenv` class. Just override what you need using the `set` or `load` methods. Note that the `load` method also understands arrays.
