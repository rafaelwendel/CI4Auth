<?php

namespace CI4Auth\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Base Authentication Configuration Class
 *
 * Defines the default settings and keys for the CI4Auth library.
 * These settings can be overridden in Config\AuthConfig class.
 */
class BaseAuthConfig extends BaseConfig
{
    /**
     * The fully qualified class name of the User Model.
     *
     * @var string
     */
    public string $authModel = 'App\Models\UserModel';

    /**
     * The model method name used to find a user by their identity (e.g., email or username).
     *
     * @var string
     */
    public string $findUserMethod = 'findUser';

    /**
     * The table column or object property that holds the hashed password.
     *
     * @var string
     */
    public string $passwordField = 'password';

    /**
     * The key in the session used to check if the user is logged in.
     *
     * @var string
     */
    public string $sessionAuthCheckKey = 'islogged';

    /**
     * Whether role-based access control is enabled.
     *
     * @var bool
     */
    public bool $enableRoles = false;

    /**
     * The session key representing the user's role/profile.
     * Required if $enableRoles is set to true (e.g. 'role' or 'profile').
     *
     * @var string
     */
    public string $roleKey = '';

    /**
     * Map of data to be saved to the session upon successful login.
     * Keys represent session keys, and values starting with 'user.' represent
     * properties/fields extracted from the retrieved User object/array.
     * Fixed values (like booleans or numbers) are written as is.
     *
     * @var array<string, mixed>
     */
    public array $sessionData = [
        'islogged'    => true,
        'userid'      => 'user.id'
        //'role'        => 'user.role' // Don't forget to set the role case $enableRoles is true
    ];

    /**
     * Whether to completely destroy the session on logout, or only remove $sessionData keys.
     *
     * @var bool
     */
    public bool $destroySessionOnLogout = false;

    /**
     * The route or URL destination to redirect to when a user is not authenticated.
     *
     * @var string
     */
    public string $notAuthenticatedRedirect = 'login';

    /**
     * The route or URL destination to redirect to when a user is not authorized (insufficient roles).
     *
     * @var string
     */
    public string $notAuthorizedRedirect = 'login';
}
