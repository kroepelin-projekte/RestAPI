<?php

namespace KPG\RestAPI\Components\ILIAS\User;


use KPG\RestAPI\API\BaseService;
use OpenApi\Attributes as OA;
use KPG\RestAPI\API\HTTP\Response;
use KPG\RestAPI\ILIAS\User\classes\UserHandler;
use KPG\RestAPI\ILIAS\User\classes\UserDataExchangeHandler;

#[OA\PathItem(
    path: "/ilias/user",
)]
#[OA\Tag(
    name: "User",
    description: "&nbsp;&nbsp;<b>Sponsor:</b> Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>Author:</b> Keven Clausen, Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>E-Mail:</b> info@kroepelin-projekte.de<br />
                  &nbsp;&nbsp;<b>Version:</b> 1.0.0",
)]
class UserService extends BaseService
{
    public function __construct(array $request_data, Response $response)
    {
        parent::__construct($request_data, $response);
    }

    #[OA\GET(
        path: '/ilias/user',
        operationId: "getAllUsers",
        description: 'Returns a list of all users in the system.',
        summary: 'Get all users',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'user_id', type: 'integer', example: 6),
                            new OA\Property(property: 'login', type: 'string', example: 'root'),
                            new OA\Property(property: 'firstname', type: 'string', example: 'Admin'),
                            new OA\Property(property: 'lastname', type: 'string', example: 'ILIAS')
                        ],
                        type: 'object'
                    )
                )
            )
        ]
    )]
    public function getAllUsers(): void
    {
        $handler = new UserHandler();
        $this->response->setResponseData($handler->getAllUser());
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/user/{user_id}',
        operationId: "getUserInformation",
        description: 'Returns detailed information for a specific user.',
        summary: 'Get user information',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'usr_id', type: 'integer', example: 6),
                        new OA\Property(property: 'login', type: 'string', example: 'root'),
                        new OA\Property(property: 'firstname', type: 'string', example: 'Admin'),
                        new OA\Property(property: 'lastname', type: 'string', example: 'ILIAS'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'root@localhost')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function getUserInformation(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $handler = new UserHandler();
        $user_data = $handler->getUserInformation($user_id);
        $this->response->setResponseData($handler->getUserInformation($user_id));
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Patch(
        path: '/ilias/user/{user_id}',
        operationId: "updateUser",
        description: 'Updates information for a specific user.',
        summary: 'Update user',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'User data to update',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                example: [
                    'firstname' => 'John',
                    'lastname' => 'Doe',
                    'email' => 'john.doe@example.com'
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function updateUser(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $update_data = $this->request_body->getAllData();
        $handler = new UserHandler();
        $handler->updateUser($user_id, $update_data);
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/user/{user_id}/course',
        operationId: "getUserCourses",
        description: 'Returns a list of courses the user is enrolled in.',
        summary: 'Get user courses',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'ref_id', type: 'integer', example: 123),
                            new OA\Property(property: 'title', type: 'string', example: 'Introduction to ILIAS'),
                            new OA\Property(
                                property: 'user_role',
                                type: 'array',
                                items: new OA\Items(type: 'string', example: 'Member')
                            )
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function getUserCourses(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $handler = new UserHandler();
        $this->response->setResponseData($handler->getUserCourses($user_id));
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/user/{user_id}/group',
        operationId: "getUserGroups",
        description: 'Returns a list of groups the user belongs to.',
        summary: 'Get user groups',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'ref_id', type: 'integer', example: 456),
                            new OA\Property(property: 'title', type: 'string', example: 'Study Group A'),
                            new OA\Property(
                                property: 'user_role',
                                type: 'array',
                                items: new OA\Items(type: 'string', example: 'Member')
                            )
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function getUserGroups(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $handler = new UserHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData($handler->getUserGroups($user_id));
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/user/{user_id}/role',
        operationId: "getUserRoles",
        description: 'Returns a list of roles assigned to the user.',
        summary: 'Get user roles',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: '2', type: 'string', example: 'Guest'),
                        new OA\Property(property: '4', type: 'string', example: 'User')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function getUserRoles(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $handler = new UserHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData($handler->getUserRoles($user_id));
        $this->response->send();
    }

    #[OA\Put(
        path: '/ilias/user/{user_id}/role/{role_id}',
        operationId: "addUserRoleEntry",
        description: 'Assigns a specific role to a user.',
        summary: 'Add user role',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "role_id",
                description: "The ID of the role to assign",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Role assigned successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User or role not found'
            )
        ]
    )]
    public function addUserRoleEntry(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $role_id = $this->path_params->getValueByKey('role_id');
        $handler = new UserHandler();
        $handler->addUserRole($user_id, $role_id);
        $this->response->setResponseCode(201);
        $this->response->send();

    }

    #[OA\DELETE(
        path: '/ilias/user/{user_id}/role/{role_id}',
        operationId: "deleteUserRoleEntry",
        description: 'Removes a specific role from a user.',
        summary: 'Remove user role',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "role_id",
                description: "The ID of the role to remove",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Role removed successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User or role assignment not found'
            )
        ]
    )]
    public function deleteUserRoleEntry(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $role_id = $this->path_params->getValueByKey('role_id');
        $handler = new UserHandler();
        $handler->removeUserRole($user_id, $role_id);
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\DELETE(
        path: '/ilias/user/{user_id}',
        operationId: "deleteUser",
        description: 'Deletes a specific user from the system.',
        summary: 'Delete user',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function deleteUser(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $handler = new UserHandler();
        $handler->deleteUser($user_id);
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/user/{user_id}/customfield',
        operationId: "getUserCustomFields",
        description: 'Returns all custom user fields and their values for a specific user.',
        summary: 'Get user custom fields',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'f_1', type: 'string', example: 'Some value'),
                        new OA\Property(property: 'f_2', type: 'string', example: 'Another value')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function getUserCustomFields(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $handler = new UserHandler();
        $this->response->setResponseData($handler->getUserDefinedValues($user_id));
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Patch(
        path: '/ilias/user/{user_id}/customfield',
        operationId: "setUserCustomFields",
        description: 'Sets or updates custom field values for a user.',
        summary: 'Set user custom fields',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Custom fields data',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                example: [
                    'my_custom_field' => 'some value'
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Custom fields updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function setUserCustomFields(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $custom_fields = $this->request_body->getAllData();
        $handler = new UserHandler();
        $handler->setUserDefinedValues($user_id, $custom_fields);
        $this->response->setResponseCode(201);
        $this->response->send();

    }

    #[OA\GET(
        path: '/ilias/user/{user_id}/customfield/{customfield}',
        operationId: "getUserCustomFieldsByCustomFieldName",
        description: 'Returns the value of a specific custom field for a user.',
        summary: 'Get user custom field by name',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "customfield",
                description: "The name of the custom field",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation'
            ),
            new OA\Response(
                response: 404,
                description: 'User or custom field not found'
            )
        ]
    )]
    public function getUserCustomFieldsByCustomFieldName(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $custom_field = $this->path_params->getValueByKey('customfield');
        $handler = new UserHandler();
        $this->response->setResponseData($handler->getUserDefinedValuesByName($user_id, $custom_field));
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/user/{user_identifier}/exists',
        operationId: "userExists",
        description: 'Checks if user with given id or username exists. Returns 200 if user exists, 404 if not.',
        summary: 'Checks if user with given id or username exists.',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status_code',
                            type: 'integer',
                            example: 200
                        ),
                        new OA\Property(
                            property: 'error_code',
                            type: 'string',
                            example: ''
                        ),
                        new OA\Property(
                            property: 'response_data',
                            properties: [],
                            type: 'object',
                            example: []
                        ),
                    ],
                    example: [
                        'status_code' => 200,
                        'error_code' => '',
                        'response_data' => [],
                    ]
                )
            )
        ]
    )]
    public function userExists(): void
    {
        $user_identifier = $this->path_params->getValueByKey('user_identifier');

        $handler = new UserHandler();
        if ($handler->userExists($user_identifier)) {

            $response_data = [];
            if (is_numeric($user_identifier)) {
                $response_data['username'] = \ilObjUser::_lookupLogin((int) $user_identifier);
                $response_data['user_id'] = (int) $user_identifier;
            } else {
                $response_data['username'] = (string) $user_identifier;
                $response_data['user_id'] = \ilObjUser::_lookupId((string) $user_identifier);
            }

            $this->response->setResponseData($response_data);
            $this->response->setResponseCode(200);
        } else {
            $this->response->setResponseCode(404);
        }
        $this->response->send();
    }

    #[OA\Post(
        path: '/ilias/user/import',
        operationId: "userImport",
        description: 'Imports multiple users from a JSON payload.',
        summary: 'Import users',
        tags: ["User"],
        requestBody: new OA\RequestBody(
            description: 'List of users to import',
            required: true,
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'login', type: 'string', example: 'jdoe'),
                        new OA\Property(property: 'passwd', type: 'string', example: 'password123'),
                        new OA\Property(property: 'firstname', type: 'string', example: 'John'),
                        new OA\Property(property: 'lastname', type: 'string', example: 'Doe'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john.doe@example.com'),
                        new OA\Property(
                            property: 'roles',
                            type: 'array',
                            items: new OA\Items(type: 'integer', example: 4)
                        ),
                        new OA\Property(
                            property: 'userdefineddata',
                            type: 'object',
                            example: ['f_1' => 'Value']
                        )
                    ],
                    type: 'object'
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Users imported successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(type: 'integer', example: 124)
                )
            )
        ]
    )]
    public function userImport(): void
    {
        $handler = new UserDataExchangeHandler();
        $import_data = $this->request_body->getAllData();
        $user_ids = $handler->importUsers($import_data);
        $this->response->setResponseData($user_ids);
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/user/{user_id}/export',
        operationId: "userExport",
        description: 'Exports user data for a specific user.',
        summary: 'Export user',
        tags: ["User"],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'login', type: 'string', example: 'jdoe'),
                            new OA\Property(property: 'firstname', type: 'string', example: 'John'),
                            new OA\Property(property: 'lastname', type: 'string', example: 'Doe'),
                            new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com'),
                            new OA\Property(
                                property: 'roles',
                                type: 'array',
                                items: new OA\Items(type: 'integer', example: 4)
                            ),
                            new OA\Property(
                                property: 'userdefineddata',
                                type: 'object',
                                example: ['f_1' => 'Value']
                            )
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found'
            )
        ]
    )]
    public function userExport(): void
    {
        $handler = new UserDataExchangeHandler();
        $export_data = $handler->exportUsers($this->path_params->getValueByKey('user_id'));
        $this->response->setResponseData($export_data);
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Post(
        path: '/ilias/user',
        operationId: "createUserWithEmail",
        description: 'Creates new user with firstname, lastname and email.',
        summary: 'Creates new user with firstname, lastname and email.',
        requestBody: new OA\RequestBody(
            description: 'User creation payload',
            required: true,
            content: new OA\JsonContent(
                required: ['firstname', 'lastname', 'email'],
                properties: [
                    new OA\Property(
                        property: 'firstname',
                        type: 'string',
                        example: 'max'
                    ),
                    new OA\Property(
                        property: 'lastname',
                        type: 'string',
                        example: 'mustermann'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'max.mustermann@example.de'
                    ),
                ],
                example: [
                    'firstname' => 'max',
                    'lastname' => 'mustermann',
                    'email' => 'max.mustermann@example.de'
                ]
            )
        ),
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status_code',
                            type: 'integer',
                            example: 200
                        ),
                        new OA\Property(
                            property: 'error_code',
                            type: 'string',
                            example: ''
                        ),
                        new OA\Property(
                            property: 'response_data',
                            properties: [
                                new OA\Property(
                                    property: 'user_id',
                                    type: 'integer',
                                    example: 123
                                ),
                                new OA\Property(
                                    property: 'username',
                                    type: 'string',
                                    example: 'max.mustermann'
                                ),
                            ],
                            type: 'object',
                            example: [
                                'user_id' => 123,
                                'username' => 'max.mustermann'
                            ]
                        ),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(
                                    property: 'total',
                                    type: 'integer',
                                    example: 2
                                ),
                            ],
                            type: 'object',
                            example: [
                                'total' => 2
                            ]
                        ),
                    ],
                    example: [
                        'status_code' => 200,
                        'error_code' => '',
                        'response_data' => [
                            'user_id' => 123,
                            'username' => 'max.mustermann'
                        ],
                        'meta' => [
                            'total' => 2
                        ]
                    ]
                )
            )
        ]
    )]
    public function createUserWithEmail(): void
    {
        $firstname = $this->request_body->getValueByKey('firstname');
        $lastname = $this->request_body->getValueByKey('lastname');
        $email = $this->request_body->getValueByKey('email');
        $handler = new UserHandler();
        $this->response->setResponseData($handler->createUserWithEmail($firstname, $lastname, $email));
        $this->response->setResponseCode(200);
        $this->response->send();
    }
}