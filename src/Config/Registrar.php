<?php

namespace CI4Auth\Config;

use CI4Auth\Services\AuthService;
use CI4Auth\Filters\AuthFilter;
use CodeIgniter\Config\BaseService;
use Config\Services;

/**
 * Registrar Config Class
 *
 * This class registers the CI4Auth services and filter aliases for CodeIgniter 4.
 * When CodeIgniter detects this class, it registers the return values of the methods here.
 */
class Registrar extends BaseService
{
    /**
     * Auth Service Registrar.
     *
     * Returns the authentication service instance.
     *
     * @param bool $getShared Whether to return a shared instance.
     *
     * @return AuthService
     */
    public static function auth(bool $getShared = false)
    {
        if ($getShared) {
            return Services::getSharedInstance('auth');
        }

        return new AuthService();
    }

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
