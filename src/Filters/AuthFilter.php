<?php

namespace CI4Auth\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Authentication and Authorization Filter
 *
 * Intercepts incoming requests to verify if a user is logged in
 * and, if roles are enabled, whether they have the required roles to proceed.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Inspects the request to verify authentication and role authorization.
     *
     * If not logged in, redirects to the configured unauthenticated landing page.
     * If roles are enabled and a role list is passed as arguments, checks if
     * the user session has one of the required roles.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments List of allowed roles for the route.
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authService = service('auth');
        $authConfig = config('AuthConfig');
        $session = service('session');

        if (!$authService->isLoggedIn()) {
            return redirect()->to($authConfig->notAuthenticatedRedirect)
                ->with('error', lang('Auth.notAuthenticatedFlashMessage'));
        }

        if ($authConfig->enableRoles && !empty($arguments)) {
            $roleKey = $authConfig->roleKey;

            $sessionRole = $session->get($roleKey);

            if (! in_array($sessionRole, $arguments)) {
                return redirect()->to($authConfig->notAuthorizedRedirect)
                    ->with('error', lang('Auth.notAuthorizedFlashMessage'));
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
