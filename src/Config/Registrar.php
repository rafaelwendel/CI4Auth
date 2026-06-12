<?php

namespace CI4Auth\Config;

use CI4Auth\Filters\AuthFilter;

/**
 * Registrar Config Class
 *
 * This class registers the CI4Auth filter aliases for CodeIgniter 4.
 * When CodeIgniter detects this class, it merges the configuration
 * returned by the Filters() method into the application's Filters config.
 */
class Registrar
{
    /**
     * Filters Registrar.
     *
     * Registers the 'auth' filter alias for routing.
     *
     * @return array<string, array<string, string>>
     */
    public static function Filters(): array
    {
        return [
            'aliases' => [
                'auth' => AuthFilter::class,
            ],
        ];
    }
}

