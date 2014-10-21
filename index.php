<?php
/*
 * Services all requests to membership api.
 */

namespace {

    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Members.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/membership/Residences.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $url = $_SERVER['REQUEST_URI'];

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
        case '/membership/residences':
            //Current support for GET methods only
            switch ($method) {
                //GET will obtain a list of residences in JSON format
                case 'GET':
                    \membership\Residences::getResidences();
                    break;
            }
    }

}
?>