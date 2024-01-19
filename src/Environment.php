<?php

declare( strict_types = 1 );

namespace Omega\Environment;

use Dotenv\Dotenv;

class Environment
{
    public function __construct()
    {
        if ( file_exists( __DIR__ . '/../../../.env' ) {
            echo "Esiste";
        } else {
            echo "Non esiste";
        }
        $dotenv = Dotenv::createImmutable( __DIR__ . '/../../.env' );
        $dotenv->load();
    }
}