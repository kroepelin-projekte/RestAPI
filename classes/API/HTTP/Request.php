<?php

namespace KPG\RestAPI\API\HTTP;

use KPG\RestAPI\API\RequestData;
use KPG\RestAPI\ILIAS\Authenticator;
use KPG\RestAPI\ILIAS\Logger\Logger;
use KPG\RestAPI\API\Exception\BaseException;

class Request
{
    private string $path_api = "/api/";
    private string $baseDir = __DIR__ . '/../../Components';

    public function route(): void
    {
        $auth = new Authenticator();
        $reponse = new Response();

        $fullUri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $apiPosition = strpos($fullUri, $this->path_api);

        if ($apiPosition === false) {
            $reponse->send404();
            return;
        }

        $requestedUri = urldecode(substr($fullUri, $apiPosition + strlen($this->path_api)));

        Logger::setRequestUrl($requestedUri);
        Logger::setHttpMethod($_SERVER['REQUEST_METHOD']);
        $requestedUri = rtrim($requestedUri, '/');
        $requestedUriParts = explode('/', $requestedUri);

        $route_file = $this->baseDir . "/" . strtoupper($requestedUriParts[0]) . "/" . ucfirst(
                $requestedUriParts[1]
            ) . "/structure/" . ucfirst($requestedUriParts[1]) . "Routes.php";

        if (!file_exists($route_file)) {
            $reponse->send404();
        }
        $routes_file = require_once($route_file);

        foreach ($routes_file['routes'] as $route) {
            if (preg_match($route['route'], "/" . $requestedUri, $matches) &&
                $route['http_method'] == $_SERVER['REQUEST_METHOD']) {
                $pathParams = [];
                foreach ($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $pathParams[$key] = $value;
                    }
                }
                $class = "KPG\\RestAPI\\Components\\" . strtoupper($requestedUriParts[0]) . "\\" . ucfirst(
                        $requestedUriParts[1]
                    ) . "\\" . ucfirst(
                        $requestedUriParts[1]
                    ) . "Service";

                if (!$class) {
                    $reponse->send500();
                }
                global $DIC;
                $user_id = $DIC->user()->getId();
                if (!$auth->checkComponentPermission(
                        $user_id, ucfirst($requestedUriParts[1]), $route['http_method']
                    ) && !$auth->checkFullAccessPermission($user_id)) {
                    $reponse->send404();
                }

                $request_body = [];
                if (array_key_exists('CONTENT_TYPE', $_SERVER)) {
                    $request_body = (array) json_decode(file_get_contents('php://input'), true);
                    if (json_last_error() != JSON_ERROR_NONE) {
                        $reponse->setResponseCode(400);
                        $reponse->setError('INVALID_JSON_BODY');
                        $reponse->send();
                    }
                }
                Logger::setRequestBody(json_encode($request_body));

                $request_data = [
                    "headers" => (new RequestData())->addData(getAllHeaders()),
                    "params" => (new RequestData())->addData($DIC->http()->request()->getQueryParams()),
                    "path_params" => (new RequestData())->addData($pathParams),
                    "request_body" => (new RequestData())->addData($request_body),
                ];

                $obj_response = new Response($request_data['params']);
                $action = $route['method'];

                $service = new $class($request_data, $obj_response);
                if (!method_exists($service, $action)) {
                    $reponse->send500();
                }
                try {
                    $service->$action();
                } catch (BaseException $e) {
                    $e->sendErrorResponse($obj_response);
                }

                return;
            }
        }

        $reponse->send404();
    }
}
