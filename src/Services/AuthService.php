<?php

namespace CI4Auth\Services;

use CodeIgniter\Session\Session;

/**
 * AuthService class
 *
 * Provides core authentication operations including validating configuration,
 * performing logins, logging out, and checking if a user is logged in.
 */
class AuthService
{
    /**
     * Session service instance.
     *
     * @var Session
     */
    private Session $session;

    /**
     * Loaded AuthConfig configuration instance.
     *
     * @var mixed
     */
    private $authConfig;

    /**
     * User Model instance.
     *
     * @var mixed
     */
    private $authModel;

    /**
     * Name of the model method to find a user.
     *
     * @var string
     */
    private $findUserMethod;

    /**
     * Constructor.
     *
     * Initializes the session, loads configuration, and validates requirements.
     *
     * @throws \RuntimeException If configuration validation fails.
     */
    public function __construct()
    {
        $this->session = service('session');
        $this->authConfig = config('AuthConfig') ?? new \CI4Auth\Config\BaseAuthConfig();

        if ($this->validateAuthConfig()) {
            $this->authModel = model($this->authConfig->authModel);
            $this->findUserMethod = $this->authConfig->findUserMethod;
        }
    }

    /**
     * Validates the authentication configuration settings.
     *
     * Ensures all required classes, methods, and configurations are defined correctly.
     *
     * @throws \RuntimeException If validation fails.
     * @return bool
     */
    private function validateAuthConfig()
    {
        $authModelClass = $this->authConfig->authModel;
        if (! class_exists($authModelClass)) {
            throw new \RuntimeException(lang('Auth.modelNotFound'));
        }

        $findUserMethod = $this->authConfig->findUserMethod;
        if (empty($findUserMethod) || ! method_exists($authModelClass, $findUserMethod)) {
            throw new \RuntimeException(lang('Auth.findUserMethodNotFound'));
        }

        // Check if $sessionAuthCheckKey is set and exists in $sessionData
        $sessionAuthCheckKey = $this->authConfig->sessionAuthCheckKey;

        if (empty($sessionAuthCheckKey)) {
            throw new \RuntimeException(lang('Auth.sessionAuthCheckKeyInvalid'));
        }

        if (! isset($this->authConfig->sessionData[$sessionAuthCheckKey])) {
            throw new \RuntimeException(lang('Auth.sessionAuthCheckKeyInvalid'));
        }

        // Check if password field is set
        if (empty($this->authConfig->passwordField)) {
            throw new \RuntimeException(lang('Auth.passwordFieldNotSet'));
        }

        // If $enableRoles is enabled, check if $roleKey is set and exists in $sessionData
        if ($this->authConfig->enableRoles) {
            $roleKey = $this->authConfig->roleKey;
            if (empty($roleKey)) {
                throw new \RuntimeException(lang('Auth.roleKeyInvalid'));
            }
            if (! isset($this->authConfig->sessionData[$roleKey])) {
                throw new \RuntimeException(lang('Auth.roleKeyInvalid'));
            }
        }

        return true;
    }

    /**
     * Attempts to log in a user with the provided credentials.
     *
     * Finds the user, verifies the password, maps requested user properties
     * to the session data structure, and writes them to the current session.
     *
     * @param string $identity The identity token (username, email, etc.)
     * @param string $password The plain-text password to verify
     *
     * @throws \RuntimeException If the password field is missing in the retrieved user object.
     * @return bool True on success, false on failure (incorrect password or user not found).
     */
    public function login(string $identity, string $password)
    {
        $user = $this->authModel->{$this->findUserMethod}($identity);

        // If user is not found, return false instead of throwing exception later.
        if (!$user) {
            return false;
        }

        $passwordField = $this->authConfig->passwordField;
        $passwordHash = null;

        if (is_object($user)) {
            if (isset($user->$passwordField)) {
                $passwordHash = $user->$passwordField;
            }
        } elseif (is_array($user)) {
            if (array_key_exists($passwordField, $user)) {
                $passwordHash = $user[$passwordField];
            }
        }

        if ($passwordHash === null) {
            throw new \RuntimeException(lang('Auth.passwordFieldNotFound', [$passwordField]));
        }

        if (password_verify($password, $passwordHash)) {
            // Get the session map
            $sessionMap = $this->authConfig->sessionData;
            $dataToSession = [];

            foreach ($sessionMap as $sessionKey => $configValue) {

                // If the value for a string starts with 'user.', extract it from the user.
                if (is_string($configValue) && strpos($configValue, 'user.') === 0) {

                    // It only retrieves the field name (e.g., 'user.profile' becomes 'profile')
                    $field = str_replace('user.', '', $configValue);

                    // Extract the value dynamically checking if $user is an object or array
                    if (is_object($user)) {
                        $dataToSession[$sessionKey] = $user->$field ?? null;
                    } elseif (is_array($user)) {
                        $dataToSession[$sessionKey] = $user[$field] ?? null;
                    }
                } else {
                    // If it doesn't start with 'user.', it's a fixed value (e.g., true, 'active', 1)
                    $dataToSession[$sessionKey] = $configValue;
                }
            }

            $this->session->set($dataToSession);
            return true;
        }

        return false;
    }

    /**
     * Logs out the current user.
     *
     * Depending on the configuration, either completely destroys the active session
     * or removes the keys mapped by $sessionData.
     * Sets a logout flash message in the session.
     *
     * @return void
     */
    public function logout()
    {
        if ($this->authConfig->destroySessionOnLogout) {
            $this->session->destroy();
        } else {
            $keysToRemove = array_keys($this->authConfig->sessionData);
            $this->session->remove($keysToRemove);
        }

        $this->session->setFlashdata('logout_message', lang('Auth.logoutFlashMessage'));
    }

    /**
     * Checks if a user is currently logged in.
     *
     * Verifies if the check key is present and not empty in the session.
     *
     * @return bool True if logged in, false otherwise.
     */
    public function isLoggedIn(): bool
    {
        $checkKey = $this->authConfig->sessionAuthCheckKey;
        return $this->session->has($checkKey) && !empty($this->session->get($checkKey));
    }
}
