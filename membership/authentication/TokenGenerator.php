<?php

namespace membership\authentication;

require_once __DIR__ . '/AuthServer.php';

class TokenGenerator {

    public static function requestToken() {
        $GLOBALS['authServer']->server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
    }
}

?>