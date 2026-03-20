<?php

namespace KPG\RestAPI\ILIAS;

use ilInitialisation;

if (PHP_SAPI === 'cli') {
    return;
}

chdir("../../../../../../../../../");

require_once 'vendor/composer/vendor/autoload.php';

class ILIASInit extends ilInitialisation
{
    public static function init()
    {
        \ilContext::init(\ilContext::CONTEXT_REST);
        \ilInitialisation::initILIAS();
        self::initGlobal('ilUser', 'ilObjUser', './Services/User/classes/class.ilObjUser.php');
        global $DIC;
        self::initAccessHandling();
        self::initAccessibilityControlConcept($DIC);
        self::initHTML();
        self::initLegalDocuments($DIC);
    }
}
