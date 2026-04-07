<?php

namespace KPG\RestAPI\ILIAS;

use KPG\RestAPI\API\HTTP\Response;
use ilAuthFrontendCredentials;
use ilAuthProviderFactory;
use ilAuthStatus;
use ilAuthFrontendFactory;
use KPG\RestAPI\ILIAS\Util\Roles;
use KPG\RestAPI\ILIAS\Database\Tables\APIPermissionTable;
use KPG\RestAPI\ILIAS\Config\Permission\PermissionModel;
use KPG\RestAPI\ILIAS\Logger\Logger;

class Authenticator
{
    use Roles;

    /**
     * Authenticates a user based on the provided credentials and checks their role permissions.
     *
     * This method retrieves the authentication headers from the request, validates the username and password,
     * and triggers the authentication process using the specified frontend. If successful, it further verifies
     * the user's role permissions. If authentication or permission checks fail, a 401 Unauthorized response is sent.
     *
     * @return bool Returns true if the user is authenticated and has the required role permissions, otherwise false.
     */
    public function auth(): bool
    {
        global $DIC;
        $headers = getallheaders();
        if (!array_key_exists('PHP_AUTH_USER', $_SERVER) or !array_key_exists('PHP_AUTH_PW', $_SERVER)) {
            (new Response())->send401();
        }
        $credentials = new ilAuthFrontendCredentials();
        $credentials->setUsername(htmlspecialchars($_SERVER['PHP_AUTH_USER']));
        $credentials->setPassword(htmlspecialchars($_SERVER['PHP_AUTH_PW']));
        $provider_factory = new ilAuthProviderFactory();
        $providers = $provider_factory->getProviders($credentials);

        $status = ilAuthStatus::getInstance();

        $frontend_factory = new ilAuthFrontendFactory();
        $frontend_factory->setContext(ilAuthFrontendFactory::CONTEXT_CLI);
        $frontend = $frontend_factory->getFrontend(
            $GLOBALS['DIC']['ilAuthSession'],
            $status,
            $credentials,
            $providers
        );
        $frontend->authenticate();

        switch ($status->getStatus()) {
            case ilAuthStatus::STATUS_AUTHENTICATED:
                return $this->checkRolePermission();
            default:
                (new Response())->send401();
                return false;
        }
    }

    /**
     * Checks if a user has permission for a specific component and HTTP method.
     *
     * @param int    $user_id        The ID of the user whose permissions are being checked.
     * @param string $component_name The name of the component to check permissions for.
     * @param string $http_method    The HTTP method to be validated (e.g., GET, POST).
     *
     * @return bool Returns true if the user has the required permission, otherwise false.
     */
    public function checkComponentPermission(int $user_id, string $component_name, string $http_method): bool
    {
        foreach (self::getGlobalRolesByUserID($user_id) as $role_id) {
            if ($role_id == "2") {
                return true;
            }
            $permissions = (new PermissionModel())->getCustomPermissionByComponentNameAndRoleID($component_name, $role_id);
            if (in_array($http_method, $permissions)) {
                return true;
            }

            if ($component_name === 'User' && $http_method === 'GET' && in_array('GET_EXISTS', $permissions)) {
                $requestedUri = urldecode(explode('?', $_SERVER['REQUEST_URI'])[0]);
                if (preg_match('#^/api/ilias/user/[^/]+/exists$#', $requestedUri)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function checkFullAccessPermission($user_id): bool
    {
        $permission_model = new PermissionModel();
        $user_roles = self::getGlobalRolesByUserID($user_id);
        foreach ($user_roles as $role_id) {
            if ($permission_model->checkFullAccessByRoleID($role_id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the current user has the necessary role permissions.
     *
     * This function retrieves all permission roles and iterates through them
     * to determine if the logged-in user has access based on their roles or
     * if they are an admin. If a valid permission is found, returns true.
     *
     * @return bool True if the user has permission, false otherwise.
     */
    public function checkRolePermission(): bool
    {
        global $DIC;

        $permission_roles = (new APIPermissionTable())->getAll();
        foreach ($permission_roles as $permission_role) {
            if ((in_array(
                        $permission_role['role_id'],
                        self::getGlobalRolesByUserID($DIC->user()->getId())
                    ) && $permission_role['permission'] != 0) || self::isUserAdmin($DIC->user()->getId())) {
                return true;
            }
        }
        return false;
    }

}
