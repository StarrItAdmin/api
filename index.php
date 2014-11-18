<?php
/*
 * Services all requests to membership api.
 */

namespace {

    include $_SERVER['DOCUMENT_ROOT'] . '/membership/authentication/TokenGenerator.php';

    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Members.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Residences.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Vehicles.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Purchases.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $url = $_SERVER['REQUEST_URI'];

    if (strpos($url, '/membership/members') !== false) {
        //Current support for POST and GET methods
        switch ($method) {
            //POST will create a new member
            case 'POST':
                \membership\Members::createMember();
                break;
            //GET will obtain a list of members in JSON format
            case 'GET':
                \membership\Members::getMembers();
                break;
        }
    } elseif (strpos($url, '/membership/member') !== false) {
        switch ($method) {
            //GET will get details of one member
            case 'GET':
                $id = $_GET['id'];
                \membership\Members::getMember($id);
                break;
            case 'PUT':
                \membership\Members::updateMember();
        }
    } elseif (strpos($url, '/membership/vehicles' !== false)) {
        //Current support for POST and GET methods
        switch ($method) {
            //POST will create a new member
            case 'POST':
                \membership\Vehicles::createVehicle();
                break;
            //GET will obtain a list of members in JSON format
            case 'GET':
                $id = $_GET['id'];
                if (is_null($id)) {
                    http_response_code(400);
                    exit(json_encode(array("error" => "No id is given to look up vehicles")));
                }
                \membership\Vehicles::getVehicles($id);
                break;
        }
    } elseif (strpos($url, '/membership/residences') !== false) {
        //Current support for GET methods only
        switch ($method) {
            //GET will obtain a list of residences in JSON format
            case 'GET':
                \membership\Residences::getResidences();
                break;
        }
    } elseif (strpos($url, '/membership/purchases') !== false) {
        //Current support for POST methods only
        switch ($method) {
            //POST will add a purchase to the transaction table.
            case 'POST':
                \membership\Purchases::addPurchase();
                break;
        }
    } elseif (strpos($url, '/authentication/token') !== false) {
        //Current support for GET method only
        switch ($method) {
            //GET will reqest an access token.
            case 'POST':
                membership\authentication\TokenGenerator::requestToken();
                break;
        }
    } elseif (strpos($url, '/authentication/validate') !== false) {
        //Current support for GET method only
        switch ($method) {
            //GET will reqest an access token.
            case 'GET':
                membership\authentication\TokenValidator::validate();
                break;
        }
    } elseif (strpos($url, '/authentication/token') !== false) {
        //Current support for GET method only
        switch ($method) {
            //GET will reqest an access token.
            case 'POST':
                membership\authentication\TokenGenerator::requestToken();
                break;
        }
    } else {
        http_response_code(404);
    }

}
?>