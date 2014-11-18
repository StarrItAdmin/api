<?php

namespace membership\authentication;

class AuthServer {
    protected $dsn = 'mysql:dbname=woshmembership;host=localhost';
    private $username = 'woshadmin';
    private $password = '1g0taw0sh';

    public function initialize() {
        // error reporting (this is a demo, after all!)
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        date_default_timezone_set('America/Los_Angeles');

        // Autoloading (composer is preferred, but for this example let's just do this)
        require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth2-server-php/src/OAuth2/Autoloader.php');
        \OAuth2\Autoloader::register();

        // $dsn is the Data Source Name for your database, for example "mysql:dbname=my_oauth2_db;host=localhost"
        $this->storage = new \OAuth2\Storage\Pdo(array('dsn' => $this->dsn,
            'username' => $this->username, 'password' => $this->password));

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new \OAuth2\Server($this->storage);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($this->storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($this->storage));
    }
}

$authServer = new AuthServer();
$authServer->initialize();

?>