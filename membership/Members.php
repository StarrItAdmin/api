<?php

namespace wosh\membership;

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';

class Members {

    /*
     * Obtains Member objects.
     */
    public static function getMembers() {
        \wosh\Utils::getJSONObjects("Select * from Members");
    }

    /*
      Parses input JSON for creation of member.
    */
    public static function createMember() {
        $jsonstring = file_get_contents('php://input');
        $json = json_decode($jsonstring,true);
        sendMemberToDB($json);
    }

    /*
      Creates a Member in the DB.
    */
    public static function sendMemberToDB($json) {
        $con = getConnection();
        $first = $json['first'];
        $last = $json['last'];
        $email = $json['email'];
        $password = md5($json['password']);
        $token = $json['token'];
        $address = $json['address'];
        $city = $json['city'];
        $state = $json['state'];
        $zip = $json['zipcode'];
        $unit = $json['unit'];
        $spaces = $json['spaces'];
        $residence = $json['residence'];
        $stmt = $con->prepare(
            "Insert into Members (first,last,email,password,token,address,city,state,zip,unit,spaces,residence) values (?,?,?,?,?,?,?,?,?,?,?,?);");
        $stmt->bind_param('sssssssssssi', $first, $last, $email, $password, $token, $address, $city, $state, $zip, $unit, $spaces, $residence);
        $stmt->execute();
        echo var_dump($con);
        closeConnection($con);
    }
}

?>