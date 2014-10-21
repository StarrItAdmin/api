<?php

namespace membership;

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';

class Vehicles {

    /*
     * Obtains Vehicle objects.
     */
    public static function getVehicles($id) {
        exit(\Utils::getJSONObjects("Select * from Vehicles where mid=" . $id . ";"));
    }

    /*
      Parses input JSON for creation of vehicle.
    */
    public static function createVehicle() {
        $jsonstring = file_get_contents('php://input');
        $json = json_decode($jsonstring,true);
        \membership\Vehicles::sendVehicleToDB($json);
    }

    /*
      Creates a Member in the DB.
    */
    public static function sendVehicleToDB($json) {
        $con = \Utils::getConnection();
        $resident = $json['member-id'];
        \Utils::checkForExists("Select * from Members where id = " . $resident . ";",
            "No such user exists as given by member-id");
        $color = $json['vehicle-color'];
        $make = $json['vehicle-make'];
        $model = $json['vehicle-model'];
        $plate = $json['vehicle-plate-number'];
        $year = $json['vehicle-year'];
        $stmt = $con->prepare(
            "Insert into Vehicles (mid,color,make,model,plate,year) values (?,?,?,?,?,?);");
        $stmt->bind_param('isssss', $resident, $color, $make, $model, $plate, $year);
        $stmt->execute();
        \Utils::closeConnection($con);
        http_response_code(200);
    }
}

?>