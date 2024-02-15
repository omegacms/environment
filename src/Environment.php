<?php

declare( strict_types = 1 );

namespace Omega\Environment;

use function Omega\Helpers\app;
use Omega\Application\Application;
use Dotenv\Dotenv;

class Environment
{
    public function __construct()
    {
        $basePath = app()->resolve( 'paths.base' );
        
        $dotenv   = Dotenv::createImmutable( $basePath );
        $dotenv->load();
    }
}