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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        path: '/ilias/user/{user_id}/exists',
        operationId: "userExists",
        description: 'Checks if user with given id exists. Returns 200 if user exists, 404 if not.',
        summary: 'Checks if user with given id exists.',
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
        $user_id = $this->path_params->getValueByKey('user_id');
        $handler = new UserHandler();
        if ($handler->userExists($user_id)) {
            $this->response->setResponseCode(200);
        } else {
            $this->response->setResponseCode(404);
        }
        $this->response->send();
    }

    #[OA\POST(
        path: '/ilias/user/import',
        operationId: "userImport",
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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
        description: 'Description',
        summary: 'Description',
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
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