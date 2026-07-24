<?php

namespace KPG\RestAPI\Components\ILIAS\Group;

use KPG\RestAPI\API\BaseService;
use OpenApi\Attributes as OA;
use KPG\RestAPI\API\HTTP\Response;
use KPG\RestAPI\ILIAS\Group\classes\GroupHandler;
use KPG\RestAPI\ILIAS\Group\classes\GroupUserHandler;

#[OA\PathItem(
    path: "/ilias/group",
)]

#[OA\Tag(
    name: "Group",
    description: "&nbsp;&nbsp;<b>Sponsor:</b> Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>Author:</b> Keven Clausen, Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>E-Mail:</b> info@kroepelin-projekte.de<br />
                  &nbsp;&nbsp;<b>Version:</b> 1.0.0",
)]
class GroupService extends BaseService
{
    public function __construct(array $request_data, Response $response)
    {
        parent::__construct($request_data, $response);
    }


    #[OA\Get(
        path: "/ilias/group",
        operationId: "getAllGroups",
        description: "Returns all Groups of the ILIAS instance",
        summary: "ILIAS Groups",
        tags: ["Group"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/LimitParameter"),
            new OA\Parameter(ref: "#/components/parameters/OffsetParameter"),
            new OA\Parameter(ref: "#/components/parameters/SearchParameter"),
            new OA\Parameter(ref: "#/components/parameters/FieldsParameter"),
            new OA\Parameter(ref: "#/components/parameters/FilterParameter"),
            new OA\Parameter(ref: "#/components/parameters/OrderParameter"),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/GetGroupsResponse", response: 200),
            new OA\Response(ref: "#/components/responses/InternalServerErrorResponse", response: 500),
            new OA\Response(ref: "#/components/responses/AuthFailedResponse", response: 401),
        ]
    )]
    public function getAllGroups(): void
    {
        $handler = new GroupHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData($handler->getGroup());
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/group/{ref_id}',
        operationId: "getGroupByRefID",
        description: 'Returns a Group of the ILIAS instance',
        summary: 'ILIAS Group',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/RefIDParamater"),
            new OA\Parameter(ref: "#/components/parameters/FieldsParameter"),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/GetGroupResponse", response: 200),
            new OA\Response(ref: "#/components/responses/InternalServerErrorResponse", response: 500),
            new OA\Response(ref: "#/components/responses/AuthFailedResponse", response: 401),
            new OA\Response(ref: "#/components/responses/GroupNotFoundResponse", response: 404),
        ]
    )]
    public function getGroupByRefID(): void
    {
        $this->response->setResponseCode(200);
        $this->response->setResponseData((new GroupHandler())->getGroup($this->path_params->getValueByKey('ref_id')));
        $this->response->send();
    }

    #[OA\Patch(
        path: '/ilias/group/{ref_id}',
        operationId: "updateGroupByRefId",
        description: 'Updates properties of a specific group.',
        summary: 'Update group',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Group data to update',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                example: [
                    'title' => 'New Group Title',
                    'description' => 'Updated description'
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Group updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            )
        ]
    )]
    public function updateGroupByRefId(): void
    {
        $handler = new GroupHandler();
        $handler->updateGroup($this->path_params->getValueByKey('ref_id'), $this->request_body->getAllData());
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/group/{ref_id}/property/{property}',
        operationId: "getGroupInformation",
        description: 'Returns a specific property of a group.',
        summary: 'Get group property',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "property",
                description: "The property to retrieve",
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
                description: 'Group or property not found'
            )
        ]
    )]
    public function getGroupInformation(): void
    {
        $group_ref_id = $this->path_params->getValueByKey('ref_id');
        $group_property = $this->path_params->getValueByKey('property');
        $handler = new GroupHandler();
        $property = $handler->getGroupInformation($group_ref_id, $group_property);
        $this->response->setResponseCode(200);
        $this->response->setResponseData([$property]);
        $this->response->send();
    }

    #[OA\Put(
        path: '/ilias/group/{ref_id}/users/{user_id}/{default_role}',
        operationId: "addUser",
        description: 'Adds a user to a group with a specific role.',
        summary: 'Add user to group',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "user_id",
                description: "The ID of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "default_role",
                description: "The role to assign (e.g., admin, member)",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User added successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Group or user not found'
            )
        ]
    )]
    public function addUser(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $group_ref_id = $this->path_params->getValueByKey('ref_id');
        $group_default_role = $this->path_params->getValueByKey('default_role');
        $handler = new GroupUserHandler();
        $handler->addUser($user_id, $group_ref_id, $group_default_role);
        $this->response->setResponseCode(201);
        $this->response->send();
    }
    #[OA\DELETE(
        path: '/ilias/group/{ref_id}/users/{user_id}',
        operationId: "deleteUser",
        description: 'Removes a user from a group.',
        summary: 'Remove user from group',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
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
                description: 'User removed successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Group or user enrollment not found'
            )
        ]
    )]
    public function deleteUser(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $group_ref_id = $this->path_params->getValueByKey('ref_id');

        $handler = new GroupUserHandler();
        $handler->deleteUser($user_id, $group_ref_id);
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/group/{ref_id}/users',
        operationId: "getAllUsers",
        description: 'Returns a list of all users in a group.',
        summary: 'Get all group users',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation'
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            )
        ]
    )]
    public function getAllUsers(): void
    {
        $group_ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new GroupUserHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData($handler->getAllUsers($group_ref_id));
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/group/{ref_id}/admins',
        operationId: "getAllAdmins",
        description: 'Returns a list of all administrators in a group.',
        summary: 'Get group admins',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Successful operation'
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            )
        ]
    )]
    public function getAllAdmins(): void
    {
        $group_ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new GroupUserHandler();
        $this->response->setResponseData($handler->getAllUsers($group_ref_id, 'admin'));
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/group/{ref_id}/members',
        operationId: "getAllMembers",
        description: 'Returns a list of all members in a group.',
        summary: 'Get group members',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Successful operation'
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            )
        ]
    )]
    public function getAllMembers(): void
    {
        $group_ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new GroupUserHandler();
        $this->response->setResponseData($handler->getAllUsers($group_ref_id, 'member'));
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/group/{ref_id}/roles',
        operationId: "getGroupRoles",
        description: 'Returns a list of roles available in a group.',
        summary: 'Get group roles',
        tags: ["Group"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the group",
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
                        new OA\Property(property: 'obj_123', type: 'string', example: 'Group Administrator'),
                        new OA\Property(property: 'obj_124', type: 'string', example: 'Group Member')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Group not found'
            )
        ]
    )]
    public function getGroupRoles(): void
    {
        $group_ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new GroupHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData($handler->getGroupRoles($group_ref_id));
        $this->response->send();
    }
}