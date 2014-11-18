<?php

namespace membership\authentication;

require_once __DIR__ . '/AuthServer.php';

class TokenValidator {

    public static function validate() {
        if (!$GLOBALS['authServer']->server->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
            $GLOBALS['authServer']->server->getResponse()->send();
            die;
        }
    }
}
?>