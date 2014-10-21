<?php

namespace wosh\membership;

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';

class Vehicles {

    /*
         * Obtains Member objects.
         */
    public static function getMembers() {
        exit(\wosh\Utils::getJSONObjects("Select * from Members"));
    }

    /*
      Parses input JSON for creation of member.
    */
    public static function createMember() {
        $jsonstring = file_get_contents('php://input');
        $json = json_decode($jsonstring,true);
        \wosh\membership\Members::sendMemberToDB($json);
    }

    /*
      Creates a Member in the DB.
    */
    public static function sendMemberToDB($json) {
        $con = \wosh\Utils::getConnection();
        $first = $json['first_name'];
        $last = $json['last_name'];
        $email = $json['email'];
        $password = md5($json['password']);
        $token = $json['token'];
        $address = $json['address'];
        $city = $json['city'];
        $state = $json['state'];
        $zip = $json['zip'];
        $residence = $json['residence'];
        $exists = \wosh\Utils::getJSONObjects("Select * from Members where email = '" . $email . "';");
        if (strlen($exists) > 2) {
            http_response_code(400);
            exit(json_encode(array("error"=>"Email address already in use")));
        }
        $stmt = $con->prepare(
            "Insert into Members (first,last,email,password,token,address,city,state,zip,residence) values (?,?,?,?,?,?,?,?,?,?);");
        $stmt->bind_param('sssssssssi', $first, $last, $email, $password, $token, $address, $city, $state, $zip, $residence);
        $stmt->execute();
        \wosh\Utils::closeConnection($con);
        exit(\wosh\Utils::getJSONObjects("Select * from Members where email = '" . $email . "';"));
    }
}

?>