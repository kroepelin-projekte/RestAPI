<?php

$routes = array (
  0 => 
  array (
    'route' => '/^\\/ilias\\/course$/',
    'http_method' => 'GET',
    'method' => 'getAllCourses',
  ),
  1 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)$/',
    'http_method' => 'GET',
    'method' => 'getCourseById',
  ),
  2 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)$/',
    'http_method' => 'PATCH',
    'method' => 'updateCourseByRefId',
  ),
  3 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/users\\/(?P<user_id>[^\\/]+)\\/(?P<default_role>[^\\/]+)$/',
    'http_method' => 'PUT',
    'method' => 'addUser',
  ),
  4 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/users$/',
    'http_method' => 'GET',
    'method' => 'getAllUsers',
  ),
  5 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/users\\/members$/',
    'http_method' => 'GET',
    'method' => 'getAllMembers',
  ),
  6 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/users\\/tutors$/',
    'http_method' => 'GET',
    'method' => 'getAllTutors',
  ),
  7 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/users\\/admin$/',
    'http_method' => 'GET',
    'method' => 'getAllAdmin',
  ),
  8 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/users\\/(?P<user_identifier>[^\\/]+)$/',
    'http_method' => 'DELETE',
    'method' => 'deleteUser',
  ),
  9 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/property\\/(?P<property>[^\\/]+)$/',
    'http_method' => 'GET',
    'method' => 'getCourseInformation',
  ),
  10 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/roles$/',
    'http_method' => 'GET',
    'method' => 'getCourseRoles',
  ),
  11 => 
  array (
    'route' => '/^\\/ilias\\/course\\/(?P<ref_id>[^\\/]+)\\/users\\/(?P<user_id>[^\\/]+)$/',
    'http_method' => 'PUT',
    'method' => 'addMember',
  ),
);

return [
    'routes' => $routes,
];
