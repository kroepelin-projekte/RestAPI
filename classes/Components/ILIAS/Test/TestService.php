<?php

namespace KPG\RestAPI\Components\ILIAS\Test;

use KPG\RestAPI\API\BaseService;
use OpenApi\Attributes as OA;
use KPG\RestAPI\API\HTTP\Response;
use KPG\RestAPI\ILIAS\Test\TestHandler;
use KPG\RestAPI\ILIAS\Test\TestMainSettingHandler;
use KPG\RestAPI\ILIAS\Test\TestGradingSystemSettingHandler;
use KPG\RestAPI\ILIAS\Test\classes\TestParticipants;
use KPG\RestAPI\ILIAS\Test\classes\TestResultHandler;

#[OA\PathItem(
    path: "/ilias/test",
)]

#[OA\Tag(
    name: "Test",
    description: "&nbsp;&nbsp;<b>Sponsor:</b> Heinrich Heine Universität<br />
                  &nbsp;&nbsp;<b>Author:</b> Keven Clausen, Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>E-Mail:</b> info@kroepelin-projekte.de<br />
                  &nbsp;&nbsp;<b>Version:</b> 1.0.0",

)]
class TestService extends BaseService
{
    public function __construct(array $request_data, Response $response)
    {
        parent::__construct($request_data, $response);
    }

    #[OA\GET(
        path: '/ilias/test',
        operationId: "getAllTests",
        description: 'Returns a list of all tests available in the system.',
        summary: 'Get all tests',
        tags: ["Test"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'ref_id', type: 'integer', example: 789),
                            new OA\Property(property: 'obj_id', type: 'integer', example: 12345),
                            new OA\Property(property: 'title', type: 'string', example: 'Final Exam'),
                            new OA\Property(property: 'parent_id', type: 'integer', example: 123)
                        ],
                        type: 'object'
                    )
                )
            )
        ]
    )]
    public function getAllTests(): void
    {
        $handler = new TestHandler();
        $this->response->setResponseData($handler->getTest());
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/test/{ref_id}',
        operationId: "getTestById",
        description: 'Returns detailed information for a specific test.',
        summary: 'Get test by ID',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
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
                        new OA\Property(property: 'ref_id', type: 'integer', example: 789),
                        new OA\Property(property: 'obj_id', type: 'integer', example: 12345),
                        new OA\Property(property: 'test_id', type: 'integer', example: 10),
                        new OA\Property(property: 'title', type: 'string', example: 'Final Exam'),
                        new OA\Property(property: 'description', type: 'string', example: 'Final exam for the course'),
                        new OA\Property(property: 'offline_status', type: 'boolean', example: false),
                        new OA\Property(property: 'owner', type: 'integer', example: 6)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Test not found'
            )
        ]
    )]
    public function getTestById(): void
    {
        $this->response->setResponseCode(200);
        $this->response->setResponseData((new TestHandler())->getTest($this->path_params->getValueByKey('ref_id')));
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/test/{ref_id}/info',
        operationId: "getTestInfoById",
        description: 'Returns basic information and status for a specific test.',
        summary: 'Get test info',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
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
                description: 'Test not found'
            )
        ]
    )]
    public function getTestInfoById(): void
    {
        $this->response->setResponseCode(200);
        $this->response->setResponseData((new TestHandler())->getTestInfo($this->path_params->getValueByKey('ref_id')));
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/test/{ref_id}/settings/general',
        operationId: "getTestSettingsGeneralById",
        description: 'Returns general settings for a test.',
        summary: 'Get test general settings',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
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
                description: 'Test not found'
            )
        ]
    )]
    public function getTestSettingsGeneralById(): void
    {
        $this->response->setResponseCode(200);
        $this->response->setResponseData((new TestMainSettingHandler())->getTestSettingsGeneral($this->path_params->getValueByKey('ref_id')));
        $this->response->send();
    }

    #[OA\PATCH(
        path: '/ilias/test/{ref_id}/settings/general',
        operationId: "updateTestSettingsGeneralById",
        description: 'Updates general settings for a test.',
        summary: 'Update test general settings',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Settings data to update',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                example: [
                    'title' => 'Updated Test Title'
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Settings updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Test not found'
            )
        ]
    )]
    public function updateTestSettingsGeneralById(): void
    {
        $this->response->setResponseCode(200);
        $update_data = $this->request_body->getAllData();
        $ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new TestMainSettingHandler();
        $handler->updateTestSettingsGeneral($ref_id, $update_data);
        $this->response->setResponseCode(201);
        $this->response->send();
    }
    #[OA\GET(
        path: '/ilias/test/{ref_id}/settings/grading-system',
        operationId: "getAllGradingByRefid",
        description: 'Returns the grading system settings for a test.',
        summary: 'Get test grading system',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
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
                description: 'Test not found'
            )
        ]
    )]
    public function getAllGradingByRefid(): void
    {
        $this->response->setResponseCode(200);
        $this->response->setResponseData((new TestGradingSystemSettingHandler())->getAllGrading($this->path_params->getValueByKey('ref_id')));
        $this->response->send();
    }

    #[OA\POST(
        path: '/ilias/test/{ref_id}/settings/grading-system',
        operationId: "addGrading",
        description: 'Adds a new grading entry to the test grading system.',
        summary: 'Add grading entry',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Grading data to add',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                example: [
                    'short_name' => 'A',
                    'long_name' => 'Excellent',
                    'percentage' => 90
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Grading entry added successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Test not found'
            )
        ]
    )]
    public function addGrading(): void
    {
        $this->response->setResponseCode(200);
        $new_grading = $this->request_body->getAllData();
        $ref_id = $this->path_params->getValueByKey('ref_id');
        (new TestGradingSystemSettingHandler())->addGrading($ref_id, $new_grading);
        $this->response->setResponseCode(201);
        $this->response->send();
    }
    #[OA\DELETE(
        path: '/ilias/test/{ref_id}/settings/grading-system/{short_name}',
        operationId: "deleteGrading",
        description: 'Deletes a specific grading entry from the test.',
        summary: 'Delete grading entry',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "short_name",
                description: "The short name of the grading entry to delete",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Grading entry deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Test or grading entry not found'
            )
        ]
    )]
    public function deleteGrading(): void
    {
        $this->response->setResponseCode(200);
        $short_name = $this->path_params->getValueByKey('short_name');
        $ref_id = $this->path_params->getValueByKey('ref_id');
        (new TestGradingSystemSettingHandler())->deleteGrading($ref_id, $short_name);
        $this->response->setResponseCode(201);
        $this->response->send();
    }
    #[OA\PATCH(
        path: '/ilias/test/{ref_id}/settings/grading-system',
        operationId: "patchGrading",
        description: 'Updates multiple grading entries for a test.',
        summary: 'Update grading entries',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'List of grading entries to update',
            required: true,
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(type: 'object')
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Grading entries updated successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Test not found'
            )
        ]
    )]
    public function patchGrading(): void
    {
        $this->response->setResponseCode(200);
        $update_grading = $this->request_body->getAllData();
        $ref_id = $this->path_params->getValueByKey('ref_id');
        (new TestGradingSystemSettingHandler())->patchGrading($ref_id, $update_grading);
        $this->response->setResponseCode(201);
        $this->response->send();
    }
    #[OA\PATCH(
        path: '/ilias/test/{ref_id}/settings/grading-system/reset',
        operationId: "resetGrading",
        description: 'Resets the test grading system to default values.',
        summary: 'Reset grading system',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Grading system reset successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Test not found'
            )
        ]
    )]
    public function resetGrading(): void
    {
        $ref_id = $this->path_params->getValueByKey('ref_id');
        (new TestGradingSystemSettingHandler())->resetGrading($ref_id);
        $this->response->setResponseCode(201);
        $this->response->send();
    }
    #[OA\GET(
        path: '/ilias/test/{ref_id}/participants',
        operationId: "getParticipants",
        description: 'Returns a list of all participants for a specific test.',
        summary: 'Get test participants',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
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
                description: 'Test not found'
            )
        ]
    )]
    public function getParticipants(): void
    {
        $ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new TestParticipants();
        $participants = $handler->getParticipants($ref_id);
        $this->response->setResponseCode(200);
        $this->response->setResponseData($participants);
        $this->response->send();
    }
    #[OA\GET(
        path: '/ilias/test/{ref_id}/results',
        operationId: "getResults",
        description: 'Returns the test results for all participants.',
        summary: 'Get all test results',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
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
                description: 'Test not found'
            )
        ]
    )]
    public function getResults(): void
    {
        $ref_id = (int) $this->path_params->getValueByKey('ref_id');
        $handler = new TestResultHandler();
        $participants = $handler->getResultsByRefId($ref_id);
        $this->response->setResponseCode(200);
        $this->response->setResponseData($participants);
        $this->response->send();
    }

    #[OA\GET(
        path: '/ilias/test/{ref_id}/results/{user_id}',
        operationId: "getResultsByUser",
        description: 'Returns the test results for a specific user.',
        summary: 'Get test results by user',
        tags: ["Test"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the test",
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
                description: 'Test or user result not found'
            )
        ]
    )]
    public function getResultsByUser(): void
    {
        $ref_id = (int) $this->path_params->getValueByKey('ref_id');
        $user_id = (int) $this->path_params->getValueByKey('user_id');
        $handler = new TestResultHandler();
        $participant = $handler->getResultsByRefIdAndUserID($ref_id, $user_id);
        $this->response->setResponseCode(200);
        $this->response->setResponseData($participant);
        $this->response->send();
    }

}