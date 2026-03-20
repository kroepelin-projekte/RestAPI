<?php

use KPG\RestAPI\ILIAS\Authenticator;
use KPG\RestAPI\ILIAS\ILIASInit;
use KPG\RestAPI\ILIAS\Logger\Logger;
use KPG\RestAPI\API\HTTP\Request;
use KPG\RestAPI\API\HTTP\Response;

if (PHP_SAPI === 'cli') {
    return;
}

require_once('ILIASInit.php');
ILIASInit::init();

try {
    $authenticator = new Authenticator();
    if ($authenticator->auth()) {
        global $DIC;
        Logger::setUserId($DIC->user()->getId());
        (new Request())->route();
    } else {
        (new Response())->send401();
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
