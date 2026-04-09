<?php

$routes = array (
  0 => 
  array (
    'route' => '/^\\/ilias\\/user$/',
    'http_method' => 'GET',
    'method' => 'getAllUsers',
  ),
  1 => 
  array (
    'route' => '/^\\/ilias\\/user$/',
    'http_method' => 'POST',
    'method' => 'createUserWithEmail',
  ),
  2 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)$/',
    'http_method' => 'GET',
    'method' => 'getUserInformation',
  ),
  3 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)$/',
    'http_method' => 'DELETE',
    'method' => 'deleteUser',
  ),
  4 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)$/',
    'http_method' => 'PATCH',
    'method' => 'updateUser',
  ),
  5 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/course$/',
    'http_method' => 'GET',
    'method' => 'getUserCourses',
  ),
  6 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/group$/',
    'http_method' => 'GET',
    'method' => 'getUserGroups',
  ),
  7 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/role$/',
    'http_method' => 'GET',
    'method' => 'getUserRoles',
  ),
  8 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/role\\/(?P<role_id>[^\\/]+)$/',
    'http_method' => 'PUT',
    'method' => 'addUserRoleEntry',
  ),
  9 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/role\\/(?P<role_id>[^\\/]+)$/',
    'http_method' => 'DELETE',
    'method' => 'deleteUserRoleEntry',
  ),
  10 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/customfield$/',
    'http_method' => 'GET',
    'method' => 'getUserCustomFields',
  ),
  11 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/customfield$/',
    'http_method' => 'PATCH',
    'method' => 'setUserCustomFields',
  ),
  12 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/customfield\\/(?P<customfield>[^\\/]+)$/',
    'http_method' => 'GET',
    'method' => 'getUserCustomFieldsByCustomFieldName',
  ),
  13 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/exists$/',
    'http_method' => 'GET',
    'method' => 'userExists',
  ),
  14 => 
  array (
    'route' => '/^\\/ilias\\/user\\/import$/',
    'http_method' => 'POST',
    'method' => 'userImport',
  ),
  15 => 
  array (
    'route' => '/^\\/ilias\\/user\\/(?P<user_id>[^\\/]+)\\/export$/',
    'http_method' => 'GET',
    'method' => 'userExport',
  ),
);

return [
    'routes' => $routes,
];
