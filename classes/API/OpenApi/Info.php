<?php
namespace KPG\RestAPI\API\OpenApi;
use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: "1.0.0",
        description: "Describes the various components and endpoints of the ILIAS REST API.",
        title: "REST API FOR ILIAS"
    ),
    servers: [
        new OA\Server(
            url: "/api",
            description: "Relative URL to the API"
        )
    ],
    security: [["basicAuth" => []]]
)]
class Info {}