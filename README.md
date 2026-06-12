# CI4Auth

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rafaelwendel/ci4auth.svg?style=flat-square)](https://packagist.org/packages/rafaelwendel/ci4auth)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP Version Require](https://img.shields.io/badge/php-%5E8.0-blue.svg?style=flat-square)](https://php.net)
[![CodeIgniter 4 Support](https://img.shields.io/badge/codeigniter-v4.x-orange.svg?style=flat-square)](https://codeigniter.com)

**CI4Auth** is a lightweight, secure, and highly flexible authentication and authorization library specifically designed for the **CodeIgniter 4** framework. It helps you seamlessly manage user logins, sessions, and role-based access control (RBAC) on your routes with minimal setup.

---

## Features

- **Quick Installation**: Spark command to set up the configuration instantly.
- **Flexible Configuration**: Map database user properties to session keys dynamically.
- **Auto-Discovery**: Automatic service registration (`service('auth')`) and filter registration (`auth`).
- **Role-Based Access Control**: Simple filter integration in routes to restrict pages based on user roles.
- **Translation Support**: Out-of-the-box translations in English and Portuguese (Brazil), customizable in the main app.
- **Session Control**: Option to destroy the complete session on logout or only remove authentication data.

---

## Requirements

*   PHP 8.0 or higher
*   CodeIgniter 4.1.0 or higher

---

## Installation

### 1. Install via Composer

Install the package using Composer inside your CodeIgniter 4 project root:

```bash
composer require rafaelwendel/ci4auth
```

### 2. Publish the Configuration

Run the Spark command to publish the default configuration template file to your application's config directory:

```bash
php spark auth:install
```

This will copy the configuration template to `app/Config/AuthConfig.php`.

---

## Configuration (`AuthConfig.php`)

Open the newly published `app/Config/AuthConfig.php` file. You can override any default properties by uncommenting them.

Here is a summary of the configuration properties:

| Property | Type | Default Value | Description |
| :--- | :--- | :--- | :--- |
| `$authModel` | `string` | `'App\Models\UserModel'` | The fully qualified class name of the User Model. |
| `$findUserMethod` | `string` | `'findUser'` | The method in the User Model used to search for the user (by email, username, etc.). |
| `$passwordField` | `string` | `'password'` | The database table column or object property that holds the hashed password. |
| `$sessionAuthCheckKey` | `string` | `'islogged'` | The session key used to check if the user is authenticated. |
| `$enableRoles` | `bool` | `false` | Set to `true` to enable role-based access control (RBAC). |
| `$roleKey` | `string` | `''` | The session key representing the user's role (e.g. `'role'`, `'profile'`). Required if `$enableRoles` is `true`. |
| `$sessionData` | `array` | See template | A key-value map of data to be saved to the session on successful login. Use `'user.field'` format to map fields from the user record. |
| `$destroySessionOnLogout` | `bool` | `false` | If `true`, calls `$session->destroy()` on logout; if `false`, only removes `$sessionData` keys. |
| `$notAuthenticatedRedirect` | `string` | `'login'` | The route or URL path to redirect unauthenticated users. |
| `$notAuthorizedRedirect` | `string` | `'login'` | The route or URL path to redirect users with insufficient role permissions. |

### Example Mapping configuration:

```php
namespace Config;

use CI4Auth\Config\BaseAuthConfig;

class AuthConfig extends BaseAuthConfig
{
    public string $authModel = 'App\Models\UserModel';
    public string $findUserMethod = 'findUser';
    public string $passwordField = 'password';
    
    public string $sessionAuthCheckKey = 'logged_in';
    public bool $enableRoles = true;
    public string $roleKey = 'user_role';

    public array $sessionData = [
        'logged_in' => true,
        'user_id'   => 'user.id',
        'user_email'=> 'user.email',
        'user_role' => 'user.role' // Required since enableRoles is true
    ];

    public bool $destroySessionOnLogout = false;
    public string $notAuthenticatedRedirect = 'auth/login';
    public string $notAuthorizedRedirect = 'dashboard/unauthorized';
}
```

---

## Usage Guide

### 1. Setting Up Your User Model

Your User Model (e.g., `App\Models\UserModel`) must contain the method specified in `$findUserMethod` (default `findUser`). This method must return the user record as an **object** or an **associative array** matching the credentials identity (e.g., email or username).

```php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'password', 'role', 'name'];

    /**
     * Finds a user by their identity (email)
     *
     * @param string $identity
     * @return array|object|null
     */
    public function findUser(string $identity)
    {
        return $this->where('email', $identity)->first();
    }
}
```

### 2. User Authentication (Login)

In your Login Controller, check the user's credentials using the `AuthService` (which is auto-registered as a service).

```php
namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
    {
        $authService = service('auth');

        if ($this->request->is('post')) {
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Attempt authentication
            if ($authService->login($email, $password)) {
                return redirect()->to('dashboard')->with('success', 'Welcome back!');
            }

            // Authentication failed
            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        return view('login_view');
    }
}
```

### 3. User Logout

To log out the user, call the `logout()` method. It automatically removes the keys defined in `$sessionData` (or destroys the session if configured) and sets a localized flash message with the key `logout_message`.

```php
namespace App\Controllers;

class AuthController extends BaseController
{
    public function logout()
    {
        service('auth')->logout();

        // Redirect to login page
        return redirect()->to('login')->with('success', session()->getFlashdata('logout_message'));
    }
}
```

---

## Authorization & Route Protection

You can protect your application routes using the auto-discovered **`AuthFilter`** (aliased as `'auth'`).

### Protecting Routes (No Roles)
To protect a group of routes so that only authenticated users can access them, apply the `'auth'` filter in your `app/Config/Routes.php` file:

```php
$routes->group('dashboard', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('profile', 'DashboardController::profile');
});
```

### Protecting Routes with Role Authorization
If `$enableRoles` is set to `true` in your configuration, you can pass authorized roles to the filter by appending them as arguments (separated by commas):

```php
// Only users with 'admin' role can access the administration panel
$routes->get('admin/settings', 'AdminController::settings', ['filter' => 'auth:admin']);

// Users with either 'admin' OR 'manager' roles can access reports
$routes->group('reports', ['filter' => 'auth:admin,manager'], function($routes) {
    $routes->get('/', 'ReportsController::index');
    $routes->get('export', 'ReportsController::export');
});
```

When a user tries to access a restricted route without the appropriate role, they are redirected to the route defined by `$notAuthorizedRedirect` with an error message stored in the `error` flashdata key.

---

## Localization & Custom Messages

CI4Auth comes with preloaded language files for Portuguese (Brazil) and English.

If you wish to customize these messages (for example, formatting flash alerts), you can override them by creating a file named `Auth.php` inside your own application directories:
- `app/Language/en/Auth.php`
- `app/Language/pt-BR/Auth.php`

Return an array containing only the keys you wish to override:

```php
// Example: app/Language/en/Auth.php
return [
    'logoutFlashMessage'           => 'You have successfully signed out.',
    'notAuthenticatedFlashMessage' => 'Access denied. Please sign in first.',
    'notAuthorizedFlashMessage'    => 'You do not have the required permissions to view this resource.',
];
```

---

## License

This library is open-source software licensed under the [MIT License](LICENSE).
