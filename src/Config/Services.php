<?php

namespace CI4Auth\Config;

use CodeIgniter\Config\BaseService;
use CI4Auth\Services\AuthService;

/**
 * Services Configuration for CI4Auth
 *
 * This class registers the CI4Auth services for CodeIgniter 4.
 */
class Services extends BaseService
{
    /**
     * Returns the authentication service instance.
     *
     * @param bool $getShared Whether to return a shared instance.
     *
     * @return AuthService
     */
    public static function auth(bool $getShared = false)
    {
        if ($getShared) {
            return static::getSharedInstance('auth');
        }

        return new AuthService();
    }
}
