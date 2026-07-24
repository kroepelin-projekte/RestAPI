<?php

namespace KPG\RestAPI\Components\ILIAS\Repository;

use KPG\RestAPI\API\BaseService;
use OpenApi\Attributes as OA;
use KPG\RestAPI\ILIAS\Repository\classes\RepositoryHandler;
use ilLPStatus;
use ilObject;
use KPG\RestAPI\ILIAS\Repository\classes\RepositoryAdvanceMetaDataHandler;
use KPG\RestAPI\ILIAS\Repository\classes\RepositoryLPHandler;

#[OA\PathItem(
    path: "/ilias/repository",
)]

#[OA\Tag(
    name: "Repository",
    description: "&nbsp;&nbsp;<b>Sponsor:</b> Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>Author:</b> Keven Clausen, Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>E-Mail:</b> info@kroepelin-projekte.de<br />
                  &nbsp;&nbsp;<b>Version:</b> 1.0.0",
)]
class RepositoryService extends BaseService
{
    #[OA\Get(
        path: '/ilias/repository/{ref_id}',
        operationId: "objectInformation",
        description: 'Returns basic information about a repository object.',
        summary: 'Get object information',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
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
                        new OA\Property(property: 'title', type: 'string', example: 'My Course'),
                        new OA\Property(property: 'description', type: 'string', example: 'A course description'),
                        new OA\Property(property: 'object_id', type: 'integer', example: 1234),
                        new OA\Property(property: 'ref_id', type: 'integer', example: 123),
                        new OA\Property(property: 'type', type: 'string', example: 'crs'),
                        new OA\Property(property: 'owner_name', type: 'string', example: 'Admin ILIAS')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Object not found'
            )
        ]
    )]
    public function objectInformation(): void
    {
        $object_ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new RepositoryHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData($handler->getObjectInformations($object_ref_id));
        $this->response->send();
    }

    #[OA\Get(
        path: '/ilias/repository/{ref_id}/exists',
        operationId: "objectExists",
        description: 'Checks if a repository object with the given reference ID exists.',
        summary: 'Check if object exists',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Object exists'
            ),
            new OA\Response(
                response: 404,
                description: 'Object does not exist'
            )
        ]
    )]
    public function objectExists(): void
    {
        $handler = new RepositoryHandler();

        if ($handler->objectExist($this->path_params->getValueByKey('ref_id'))) {
            $this->response->setResponseCode(200);
        } else {
            $this->response->setResponseCode(404);
        }
        $this->response->send();
    }

    #[OA\Get(
        path: '/ilias/repository/{ref_id}/type',
        operationId: "getObjectType",
        description: 'Returns the type of a repository object (e.g., crs, grp, fold).',
        summary: 'Get object type',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
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
                description: 'Object not found'
            )
        ]
    )]
    public function getObjectType(): void
    {
        $handler = new RepositoryHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData(
            ["type" => $handler->getObjectType($object_ref_id = $this->path_params->getValueByKey('ref_id'))]
        );
        $this->response->send();
    }

    # ToDo ab hier
    #[OA\Delete(
        path: '/ilias/repository/{ref_id}',
        operationId: "deleteObject",
        description: 'Deletes a repository object.',
        summary: 'Delete object',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object to delete",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Object deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Object not found'
            )
        ]
    )]
    public function deleteObject(): void
    {
        $handler = new RepositoryHandler();
        $handler->deleteObject($this->path_params->getValueByKey('ref_id'));
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\Get(
        path: '/ilias/repository/{ref_id}/parent',
        operationId: "getParent",
        description: 'Returns the parent reference ID of an object.',
        summary: 'Get parent object',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
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
                description: 'Object not found'
            )
        ]
    )]
    public function getParent(): void
    {
        $handler = new RepositoryHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData(["parent" => $handler->getParent($this->path_params->getValueByKey('ref_id'))]
        );
        $this->response->send();
    }

    #[OA\Get(
        path: '/ilias/repository/{ref_id}/childrens',
        operationId: "getChildren",
        description: 'Returns a list of all children (sub-objects) of a repository object.',
        summary: 'Get child objects',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
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
                description: 'Object not found'
            )
        ]
    )]
    public function getChildren(): void
    {
        $handler = new RepositoryHandler();
        $this->response->setResponseCode(200);
        $this->response->setResponseData(
            ["childrens" => $handler->getChildrens($this->path_params->getValueByKey('ref_id'))]
        );
        $this->response->send();
    }


    #[OA\PUT(
        path: '/ilias/repository/{ref_id}/copy/{target_ref_id}',
        operationId: "copyObject",
        description: 'Copies a repository object to a target location.',
        summary: 'Copy object',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object to copy",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "target_ref_id",
                description: "The reference ID of the target location",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Object copied successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Source or target location not found'
            )
        ]
    )]
    public function copyObject(): void
    {
        $obj_ref_id = $this->path_params->getValueByKey('ref_id');
        $obj_target_ref_id = $this->path_params->getValueByKey('target_ref_id');
        $object = new \ilObject();
        $object->setRefId($obj_ref_id);
        $object->setType(\ilObject::_lookupType($obj_ref_id, true));
        $object->read();
        $object->cloneObject($obj_target_ref_id);

        $this->response->setResponseCode(201);
        $this->response->send();
    }


    #[OA\GET(
        path: '/ilias/repository/{ref_id}/advancedmetadata',
        operationId: "getAdvancedMetaData",
        description: 'Returns advanced meta data for a repository object.',
        summary: 'Get advanced meta data',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
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
                description: 'Object not found'
            )
        ]
    )]
    public function getAdvancedMetaData(): void
    {
        $ref_id = (int) $this->path_params->getValueByKey('ref_id');
        $meta_data_handler = new RepositoryAdvanceMetaDataHandler();
        $this->response->setResponseData($meta_data_handler->getAdvanceMetaDataByRefID($ref_id));
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/repository/{ref_id}/learninghistory/users',
        operationId: "getLearningHistoryUsers",
        description: 'Returns the learning progress/history for all users in a repository object.',
        summary: 'Get learning history for users',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
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
                description: 'Object not found'
            )
        ]
    )]
    public function getLearningHistoryUsers(): void
    {
        $obj_ref_id = $this->path_params->getValueByKey('ref_id');
        $lp_handler = new RepositoryLPHandler();
        $this->response->setResponseData($lp_handler->getLPByRefID((int) $obj_ref_id));
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/repository/{ref_id}/learninghistory/users/{user_id}',
        operationId: "getLearningHistoryByUserID",
        description: 'Returns the learning progress/history for a specific user in a repository object.',
        summary: 'Get user learning history',
        tags: ["Repository"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the object",
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
                response: 200,
                description: 'Successful operation'
            ),
            new OA\Response(
                response: 404,
                description: 'Object or user not found'
            )
        ]
    )]
    public function getLearningHistoryByUserID(): void
    {
        $obj_ref_id = (int) $this->path_params->getValueByKey('ref_id');
        $user_id = (int) $this->path_params->getValueByKey('user_id');

        $lp_handler = new RepositoryLPHandler();

        $this->response->setResponseData(['status' => $lp_handler->getLPByRefIDAndUserID($obj_ref_id, $user_id)]);
        $this->response->setResponseCode(200);
        $this->response->send();
    }
}