<?php

namespace KPG\RestAPI\Components\ILIAS\Course;

use OpenApi\Attributes as OA;
use KPG\RestAPI\API\BaseService;
use KPG\RestAPI\API\HTTP\Response;
use KPG\RestAPI\Components\ILIAS\Course\classes\CourseHandler;
use KPG\RestAPI\ILIAS\Course\classes\CourseUserHandler;
use KPG\RestAPI\API\Exception\BaseException;

#[OA\PathItem(
    path: "/ilias/course",
)]

#[OA\Tag(
    name: "Course",
    description: "&nbsp;&nbsp;<b>Sponsor:</b> Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>Author:</b> Keven Clausen, Kröpelin Projekt GmbH<br />
                  &nbsp;&nbsp;<b>E-Mail:</b> info@kroepelin-projekte.de<br />
                  &nbsp;&nbsp;<b>Version:</b> 1.0.0",
)]
class CourseService extends BaseService
{
    public function __construct(array $request_data, Response $response)
    {
        parent::__construct($request_data, $response);
    }
    #[OA\Get(
        path: "/ilias/course",
        operationId: "getAllCourses",
        description: 'Retrieve all courses from the ILIAS instance that are neither deleted nor in the trash.',
        summary: "Get all courses",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/LimitParameter"),
            new OA\Parameter(ref: "#/components/parameters/OffsetParameter"),
            new OA\Parameter(ref: "#/components/parameters/SearchParameter"),
            new OA\Parameter(ref: "#/components/parameters/FieldsParameter"),
            new OA\Parameter(ref: "#/components/parameters/FilterParameter"),
            new OA\Parameter(ref: "#/components/parameters/OrderParameter"),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/GetCoursesResponse", response: 200),
            new OA\Response(ref: "#/components/responses/InternalServerErrorResponse", response: 500),
            new OA\Response(ref: "#/components/responses/AuthFailedResponse", response: 401),
        ]
    )]
    public function getAllCourses(): void
    {
        $handler = new CourseHandler();
        $this->response->setResponseCode(200);
        $result = $handler->getCourse();
        if(!empty($result)) {
            $this->response->setResponseData($result);
        }
        $this->response->send();
    }
    #[OA\GET(
        path: "/ilias/course/{ref_id}",
        operationId: "getCourseById",
        description: "Get Course By Ref_id",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(ref: "#/components/parameters/RefIDParamater"),
            new OA\Parameter(ref: "#/components/parameters/FieldsParameter"),
        ],
        responses: [
            new OA\Response(ref: "#/components/responses/GetCoursesResponse", response: 200),
            new OA\Response(ref: "#/components/responses/InternalServerErrorResponse", response: 500),
            new OA\Response(ref: "#/components/responses/AuthFailedResponse", response: 401),
            new OA\Response(ref: "#/components/responses/CourseNotFoundResponse", response: 404),
        ]
    )]
    public function getCourseById(): void
    {
        $this->response->setResponseCode(200);
        $this->response->setResponseData((new CourseHandler())->getCourse($this->path_params->getValueByKey('ref_id')));
        $this->response->send();
    }

    #[OA\Patch(
        path: "/ilias/course/{ref_id}",
        operationId: "updateCourseByRefId",
        description: "Update Course by Setter Methods",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The Ref ID of the course",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\RequestBody(
                description: "JSON object containing the fields to update",
                required: true,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "showMembersExport",
                            description: "Whether members export should be shown",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "registrationAccessCode",
                            description: "The registration access code for the course",
                            type: "string",
                            example: "REG123"
                        ),
                        new OA\Property(
                            property: "importantInformation",
                            description: "Important information about the course",
                            type: "string",
                            example: "Important course details"
                        ),
                        new OA\Property(
                            property: "syllabus",
                            description: "Syllabus details",
                            type: "string",
                            example: "Course syllabus content"
                        ),
                        new OA\Property(
                            property: "targetGroup",
                            description: "The target group for the course",
                            type: "string",
                            example: "IT Professionals"
                        ),
                        new OA\Property(
                            property: "contactName",
                            description: "Name of the course contact",
                            type: "string",
                            example: "John Doe"
                        ),
                        new OA\Property(
                            property: "contactConsultation",
                            description: "Consultation details for the contact",
                            type: "string",
                            example: "Every Monday, 10:00 to 12:00"
                        ),
                        new OA\Property(
                            property: "contactPhone",
                            description: "Phone number of the contact person",
                            type: "string",
                            example: "+49 123 456 789"
                        ),
                        new OA\Property(
                            property: "contactEmail",
                            description: "Email of the contact person",
                            type: "string",
                            example: "contact@example.com"
                        ),
                        new OA\Property(
                            property: "contactResponsibility",
                            description: "Responsibility of the contact person",
                            type: "string",
                            example: "Course coordinator"
                        ),
                        new OA\Property(
                            property: "activationStart",
                            description: "Activation start time (timestamp)",
                            type: "integer",
                            example: 1681234567
                        ),
                        new OA\Property(
                            property: "activationEnd",
                            description: "Activation end time (timestamp)",
                            type: "integer",
                            example: 1689876543
                        ),
                        new OA\Property(
                            property: "activationVisibility",
                            description: "Visibility of activation",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "subscriptionLimitationType",
                            description: "Type of subscription limitation",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "subscriptionStart",
                            description: "Subscription start time (timestamp)",
                            type: "integer",
                            example: 1681234567
                        ),
                        new OA\Property(
                            property: "subscriptionEnd",
                            description: "Subscription end time (timestamp)",
                            type: "integer",
                            example: 1689876543
                        ),
                        new OA\Property(
                            property: "subscriptionType",
                            description: "Type of subscription",
                            type: "integer",
                            example: 2
                        ),
                        new OA\Property(
                            property: "subscriptionPassword",
                            description: "Password required for subscription",
                            type: "string",
                            example: "password123"
                        ),
                        new OA\Property(
                            property: "numberOfPreviousSessions",
                            description: "Number of previous sessions to show",
                            type: "integer",
                            example: 5
                        ),
                        new OA\Property(
                            property: "numberOfNextSessions",
                            description: "Number of upcoming sessions to show",
                            type: "integer",
                            example: 3
                        ),
                        new OA\Property(
                            property: "subscriptionMaxMembers",
                            description: "Maximum number of members allowed in the subscription",
                            type: "integer",
                            example: 100
                        ),
                        new OA\Property(
                            property: "viewMode",
                            description: "View mode for the course",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "timingMode",
                            description: "Timing mode for the course",
                            type: "integer",
                            example: 2
                        ),
                        new OA\Property(
                            property: "aboStatus",
                            description: "Abo status of the course",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "showMembers",
                            description: "Whether to show members",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "mailToMembersType",
                            description: "Mail type sent to members",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "message",
                            description: "Course message",
                            type: "string",
                            example: "Welcome to the course"
                        ),
                        new OA\Property(
                            property: "latitude",
                            description: "Latitude of the course location",
                            type: "string",
                            example: "52.5200"
                        ),
                        new OA\Property(
                            property: "longitude",
                            description: "Longitude of the course location",
                            type: "string",
                            example: "13.4050"
                        ),
                        new OA\Property(
                            property: "locationZoom",
                            description: "Zoom level for the course location",
                            type: "integer",
                            example: 15
                        ),
                        new OA\Property(
                            property: "enableCourseMap",
                            description: "Enable course map",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "coursePeriod",
                            description: "The period of the course",
                            properties: [
                                new OA\Property(
                                    property: "start",
                                    type: "string",
                                    format: "date-time",
                                    example: "2023-07-01T00:00:00Z"
                                ),
                                new OA\Property(
                                    property: "end",
                                    type: "string",
                                    format: "date-time",
                                    example: "2023-12-31T23:59:59Z"
                                )
                            ],
                            type: "object"
                        ),
                        new OA\Property(
                            property: "cancellationEnd",
                            description: "The cancellation end date",
                            type: "string",
                            format: "date",
                            example: "2023-12-01"
                        ),
                        new OA\Property(
                            property: "subscriptionMinMembers",
                            description: "Minimum number of members required for subscription",
                            type: "integer",
                            example: 10
                        ),
                        new OA\Property(
                            property: "waitingListAutoFill",
                            description: "Waiting list autofill status",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "parentRolePermissions",
                            description: "Parent role permissions ID",
                            type: "integer",
                            example: 1234
                        ),
                        new OA\Property(
                            property: "autoNotification",
                            description: "Enable or disable auto notifications",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "statusDetermination",
                            description: "Status determination type",
                            type: "integer",
                            example: 1
                        ),
                        new OA\Property(
                            property: "objectTranslation",
                            description: "Object translation details",
                            type: "string",
                            example: "{\"de\": \"Kurs\", \"en\": \"Course\"}"
                        ),
                        new OA\Property(
                            property: "hiddenFilesFound",
                            description: "Status of hidden files found",
                            type: "boolean",
                            example: false
                        ),
                        new OA\Property(
                            property: "styleSheetId",
                            description: "Stylesheet ID for the course",
                            type: "integer",
                            example: 101
                        ),
                        new OA\Property(
                            property: "newsTimeline",
                            description: "Enable or disable news timeline",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "newsTimelineAutoEntries",
                            description: "Enable or disable auto entries in news timeline",
                            type: "boolean",
                            example: false
                        ),
                        new OA\Property(
                            property: "newsTimelineLandingPage",
                            description: "Enable or disable news timeline on the landing page",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "newsBlockActivated",
                            description: "Enable or disable the news block",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "useNews",
                            description: "Enable or disable the use of news",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "orderType",
                            description: "Order type for the course",
                            type: "integer",
                            example: 3
                        ),
                        new OA\Property(
                            property: "id",
                            description: "Course ID",
                            type: "integer",
                            example: 1001
                        ),
                        new OA\Property(
                            property: "refId",
                            description: "Course reference ID",
                            type: "integer",
                            example: 1234
                        ),
                        new OA\Property(
                            property: "type",
                            description: "Course type",
                            type: "string",
                            example: "online"
                        ),
                        new OA\Property(
                            property: "title",
                            description: "Course title",
                            type: "string",
                            example: "Advanced PHP Programming"
                        ),
                        new OA\Property(
                            property: "description",
                            description: "Description of the course",
                            type: "string",
                            example: "This course covers advanced PHP programming techniques."
                        ),
                        new OA\Property(
                            property: "importId",
                            description: "Import ID of the course",
                            type: "string",
                            example: "IMPORT1234"
                        ),
                        new OA\Property(
                            property: "offlineStatus",
                            description: "Offline status of the course",
                            type: "boolean",
                            example: false
                        ),
                        new OA\Property(
                            property: "owner",
                            description: "ID of the course owner",
                            type: "integer",
                            example: 2001
                        ),
                        new OA\Property(
                            property: "deletedDates",
                            description: "Deleted dates related to the course",
                            type: "array",
                            items: new OA\Items(type: "integer"),
                            example: [101, 102]
                        ),
                        new OA\Property(
                            property: "permissions",
                            description: "Parent reference permissions",
                            type: "integer",
                            example: 1
                        )
                    ],
                    type: "object"
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: "Update war erfolgreich",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "status_code",
                            description: "HTTP Response Code",
                            type: "integer",
                            example: 201
                        ),
                        new OA\Property(
                            property: "error_code",
                            description: "Error Code (not applicable for successful requests)",
                            type: "string",
                            example: null,
                            nullable: true
                        ),
                        new OA\Property(
                            property: "response_data",
                            description: "Response Data",
                            type: "object",
                            example: []
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "status_code",
                            description: "HTTP Response Code",
                            type: "integer",
                            example: 404
                        ),
                        new OA\Property(
                            property: "error_code",
                            description: "The specific error code for this failure",
                            type: "string",
                            example: "COURSE_NOT_FOUND"
                        ),
                        new OA\Property(
                            property: "response_data",
                            description: "Response Data",
                            type: "object",
                            example: []
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "One or more of the methods used in the request are not supported",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "status_code",
                            description: "HTTP Response Code",
                            type: "integer",
                            example: 400
                        ),
                        new OA\Property(
                            property: "error_code",
                            description: "The specific error code for this failure",
                            type: "string",
                            example: "METHOD_NOT_FOUND"
                        ),
                        new OA\Property(
                            property: "response_data",
                            description: "Response Data",
                            properties: [
                                new OA\Property(
                                    property: "failed_methods",
                                    description: "List of invalid or non-existent methods",
                                    type: "string",
                                    example: ["method_1, method_2"]
                                )
                            ],
                            type: "object"
                        )
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function updateCourseByRefId(): void
    {
        $handler = new CourseHandler();
        $handler->updateCourse($this->path_params->getValueByKey('ref_id'), $this->request_body->getAllData());
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\Put(
        path: "/ilias/course/{ref_id}/users/{user_id}/{default_role}",
        operationId: "addUser",
        description: "Adds a user to a course with a specific role",
        summary: "Add a user to a course",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "Ref_id of the course",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "user_id",
                description: "Id of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "default_role",
                description: "Default role of the course. admin, tutor, member",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "success add user to course"
            ),
            new OA\Response(
                response: 404,
                description: "Error",
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: 'COURSE_NOT_FOUND',
                            summary: "COURSE_NOT_FOUND",
                            description: "Course not found"
                        ),
                        new OA\Examples(
                            example: 'USER_NOT_FOUND',
                            summary: "USER_NOT_FOUND",
                            description: "User not found"
                        )
                    ]
                )
            ),
        ]
    )]
    public function addUser(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $course_ref_id = $this->path_params->getValueByKey('ref_id');
        $course_default_role = $this->path_params->getValueByKey('default_role');
        $handler = new CourseUserHandler();
        $handler->addUser($user_id, $course_ref_id, $course_default_role);
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\Get(
        path: "/ilias/course/{ref_id}/users",
        operationId: "getAllUsers",
        description: "Retrieves all users associated with the given course ID.",
        summary: "Get all users of a course",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the course.",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users retrieved successfully.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: "user_id",
                                description: "The ID of the user.",
                                type: "integer"
                            ),
                            new OA\Property(
                                property: "login",
                                description: "The login name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "firstname",
                                description: "The first name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "lastname",
                                description: "The last name of the user.",
                                type: "string"
                            ),
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found.",
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: "COURSE_NOT_FOUND",
                            summary: "Course not found",
                            description: "The course with the provided ref_id does not exist."
                        )
                    ]
                )
            ),
        ]
    )]
    public function getAllUsers(): void
    {
        $handler = new CourseUserHandler();
        $result = $handler->getAllUsers($this->path_params->getValueByKey('ref_id'));
        if(!empty($result)) {
            $this->response->setResponseData($result);
        }
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Get(
        path: "/ilias/course/{ref_id}/users/members",
        operationId: "getAllMembers",
        description: "Retrieves all Members associated with the given course ID.",
        summary: "Get all Members of a course",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the course.",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users retrieved successfully.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: "user_id",
                                description: "The ID of the user.",
                                type: "integer"
                            ),
                            new OA\Property(
                                property: "login",
                                description: "The login name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "firstname",
                                description: "The first name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "lastname",
                                description: "The last name of the user.",
                                type: "string"
                            ),
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found.",
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: "COURSE_NOT_FOUND",
                            summary: "Course not found",
                            description: "The course with the provided ref_id does not exist."
                        )
                    ]
                )
            ),
        ]
    )]
    public function getAllMembers(): void
    {
        $handler = new CourseUserHandler();
        $result = $handler->getAllUsers($this->path_params->getValueByKey('ref_id'), "member");
        if(!empty($result)) {
            $this->response->setResponseData($result);
        }
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Get(
        path: "/ilias/course/{ref_id}/users/tutors",
        operationId: "getAllTutors",
        description: "Retrieves all Tutors associated with the given course ID.",
        summary: "Get all Tutors of a course",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the course.",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users retrieved successfully.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: "user_id",
                                description: "The ID of the user.",
                                type: "integer"
                            ),
                            new OA\Property(
                                property: "login",
                                description: "The login name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "firstname",
                                description: "The first name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "lastname",
                                description: "The last name of the user.",
                                type: "string"
                            ),
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found.",
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: "COURSE_NOT_FOUND",
                            summary: "Course not found",
                            description: "The course with the provided ref_id does not exist."
                        )
                    ]
                )
            ),
        ]
    )]
    public function getAllTutors(): void
    {
        $handler = new CourseUserHandler();
        $result = $handler->getAllUsers($this->path_params->getValueByKey('ref_id'), "tutor");
        if(!empty($result)) {
            $this->response->setResponseData($result);
        }
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Get(
        path: "/ilias/course/{ref_id}/users/admin",
        operationId: "getAllAdmin",
        description: "Retrieves all Admins associated with the given course ID.",
        summary: "Get all Admins of a course",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the course.",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users retrieved successfully.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: "user_id",
                                description: "The ID of the user.",
                                type: "integer"
                            ),
                            new OA\Property(
                                property: "login",
                                description: "The login name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "firstname",
                                description: "The first name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "lastname",
                                description: "The last name of the user.",
                                type: "string"
                            ),
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found.",
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: "COURSE_NOT_FOUND",
                            summary: "Course not found",
                            description: "The course with the provided ref_id does not exist."
                        )
                    ]
                )
            ),
        ]
    )]
    public function getAllAdmin(): void
    {
        $handler = new CourseUserHandler();
        $result = $handler->getAllUsers($this->path_params->getValueByKey('ref_id'), "admin");
        if(!empty($result)) {
            $this->response->setResponseData($result);
        }
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Delete(
        path: "/ilias/course/{ref_id}/users/{user_identifier}",
        operationId: "deleteUser",
        description: "Removes a user from a course by user_id or username.",
        summary: "Removes a user from a course by user_id or username.",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "Ref_id of the course",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "user_identifier",
                description: "ID or username of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "success add user to course"
            ),
            new OA\Response(
                response: 404,
                description: "Error",
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: 'COURSE_NOT_FOUND',
                            summary: "COURSE_NOT_FOUND",
                            description: "Course not found"
                        ),
                        new OA\Examples(
                            example: 'USER_NOT_FOUND',
                            summary: "USER_NOT_FOUND",
                            description: "User not found"
                        )
                    ]
                )
            ),
        ]
    )]
    public function deleteUser(): void
    {
        $user_id_or_username = $this->path_params->getValueByKey('user_identifier');
        $course_ref_id = $this->path_params->getValueByKey('ref_id');

        $handler = new CourseUserHandler();
        $handler->deleteUser($user_id_or_username, $course_ref_id);
        $this->response->setResponseCode(201);
        $this->response->send();
    }

    #[OA\Get(
        path: "/ilias/course/{ref_id}/property/{property}",
        operationId: "getCourseInformation",
        description: "DESCRIPTION",
        summary: "DESCRIPTION",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "The reference ID of the course.",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "property",
                description: "The Property of the course.",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users retrieved successfully.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: "user_id",
                                description: "The ID of the user.",
                                type: "integer"
                            ),
                            new OA\Property(
                                property: "login",
                                description: "The login name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "firstname",
                                description: "The first name of the user.",
                                type: "string"
                            ),
                            new OA\Property(
                                property: "lastname",
                                description: "The last name of the user.",
                                type: "string"
                            ),
                        ],
                        type: "object"
                    )
                )
            )
        ])]
    public function getCourseInformation(): void
    {
        $course_ref_id = $this->path_params->getValueByKey('ref_id');
        $course_property = $this->path_params->getValueByKey('property');
        $handler = new CourseHandler();
        $result = $handler->getCourseInformation($course_ref_id, $course_property);
        if(!empty([$result])) {
            $this->response->setResponseData([$result]);
        }
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Get(
        path: '/ilias/course/{ref_id}/roles',
        operationId: "getCourseRoles",
        description: 'Description',
        summary: 'Description',
        tags: ["Course"],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Erfolgreiche Antwort'
            )
        ]
    )]
    public function getCourseRoles(): void
    {
        $course_ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new CourseHandler();
        $result = $handler->getCourseRoles($course_ref_id);
        if(!empty($result)) {
            $this->response->setResponseData($result);
        }
        $this->response->setResponseCode(200);
        $this->response->send();
    }

    #[OA\Put(
        path: "/ilias/course/{ref_id}/users/{user_id}",
        operationId: "addMember",
        description: "Adds a user to a course as member",
        summary: "Add a user to a course as member",
        tags: ["Course"],
        parameters: [
            new OA\Parameter(
                name: "ref_id",
                description: "Ref_id of the course",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "user_id",
                description: "Id of the user",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "success add user to course"
            ),
            new OA\Response(
                response: 404,
                description: "Error",
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: 'COURSE_NOT_FOUND',
                            summary: "COURSE_NOT_FOUND",
                            description: "Course not found"
                        ),
                        new OA\Examples(
                            example: 'USER_NOT_FOUND',
                            summary: "USER_NOT_FOUND",
                            description: "User not found"
                        )
                    ]
                )
            ),
        ]
    )]
    public function addMember(): void
    {
        $user_id = $this->path_params->getValueByKey('user_id');
        $course_ref_id = $this->path_params->getValueByKey('ref_id');
        $handler = new CourseUserHandler();
        $handler->addMember($user_id, $course_ref_id);
        $this->response->setResponseCode(201);
        $this->response->send();
    }
}

