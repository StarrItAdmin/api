<?php

namespace membership;
date_default_timezone_set('America/Los_Angeles');

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';

class Purchases
{

    /*
      Adds a Purchase for a Member based on Member id and Residence_Plan id.
    */
    public static function addPurchase() {
        $jsonstring = file_get_contents('php://input');
        $json = json_decode($jsonstring,true);
        \membership\Purchases::sendPurchaseToDB($json);
    }

    /*
      Creates a Purchase in the DB purchases transaction table.
      Obtains member id and residence_plan id from input JSON.
    */
    public static function sendPurchaseToDB($json) {
        $con = \Utils::getConnection();
        $member = $json['member_id'];
        $plan = $json['plan_id'];
        $stmt = $con->prepare(
            "Insert into Purchases (member_id,res_plan_id,date) values (?,?,?);");
        $stmt->bind_param('iis', $member, $plan, date('Y-m-d H:i:s'));
        $stmt->execute();
        \Utils::closeConnection($con);
        http_response_code(200);
        exit(json_encode(array("success" => "success")));
    }
}
?>
