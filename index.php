<?php
/*
 * Services all requests to membership api.
 */

namespace {

    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Members.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Residences.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Vehicles.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Purchases.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $url = $_SERVER['REQUEST_URI'];
    $question = strrpos($url, '?');
    if ($question > 0) {
        $secondpiece = substr($url, $question);
    } else {
        $secondpiece = "";
    }
    switch ($url) {
        case '/membership/members':
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
        case '/membership/vehicles' . $secondpiece:
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
                        exit(json_encode(array("error"=>"No id is given to look up vehicles")));
                    }
                    \membership\Vehicles::getVehicles($id);
                    break;
            }
        case '/membership/residences':
            //Current support for GET methods only
            switch ($method) {
                //GET will obtain a list of residences in JSON format
                case 'GET':
                    \membership\Residences::getResidences();
                    break;
            }

        case '/membership/purchases':
            //Current support for POST methods only
            switch ($method) {
                //POST will add a purchase to the transaction table.
                case 'POST':
                    \membership\Purchases::addPurchase();
                    break;
            }
    }

}
?>