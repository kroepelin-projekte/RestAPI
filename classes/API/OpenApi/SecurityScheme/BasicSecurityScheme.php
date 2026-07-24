<?php
namespace KPG\RestAPI\API\OpenApi\SecurityScheme;

use OpenApi\Attributes as OA;

#[OA\SecurityScheme(
    securityScheme: "basicAuth",
    type: "http",
    scheme: "basic",
    description: "Standard HTTP Basic Authentication using ILIAS credentials."
)]
final class BasicSecurityScheme{}