<?php

namespace membership;

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/membership/authentication/TokenValidator.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Stripe.php';

class Members {

    /*
     * Obtains Member objects.
     */
    public static function getMembers() {
        //\membership\authentication\TokenValidator::validate();
        exit(\Utils::getJSONObjects("Select * from Members"));
    }

    public static function getMember($id) {
        //\membership\authentication\TokenValidator::validate();
        exit(\Utils::getJSONObject("Select * from Members where id = ?", $id));
    }

    public static function updateMember() {
        //\membership\authentication\TokenValidator::validate();
        $jsonstring = file_get_contents('php://input');
        $json = json_decode($jsonstring, true);
        if (!isset($json['id'])) {
            http_response_code(400);
            exit(json_encode(array("error" => "In order to update a Member, id must be present")));
        }
        $id = $json['id'];

        \Utils::updateObject($id, "Members", $json, array("first", "last",
            "residence", "address", "city", "state", "zip", "unit", "spaces", "password"));
        exit(\Utils::getJSONObject("Select * from Members where id = ?", $id));
    }

    /*
      Parses input JSON for creation of member.

    */
    public static function createMember() {
        $jsonstring = file_get_contents('php://input');
        $json = json_decode($jsonstring,true);
        \membership\Members::sendMemberToDB($json);
        $residence = \membership\Residences::getResidence($json['residence']);
        $res_plan = json_decode($residence, true)['plan_name'];
        \membership\Members::createStripeCustomer($json, $res_plan);
        exit(\Utils::getJSONObject("Select * from Members where email = '" . $json['email'] . "';"));
    }

	public static function createStripeCustomer($json, $res_plan){
		\Stripe::setApiKey("sk_test_NTjhMNYlAF8QelmkFbj1hbld");

		// Get the credit card details submitted by the form
		\Stripe_Customer::create(array(
				"card" => $json['token'],
				"plan" => $res_plan,
				"email" => $json['email'])
		);
	}

    /*
      Creates a Member in the DB.
    */
    public static function sendMemberToDB($json) {
        $con = \Utils::getConnection();
        $first = $json['first_name'];
        $last = $json['last_name'];
        $email = $json['email'];
        $password = md5($json['password']);
        $token = $json['token'];
        $address = isset($json['address']) ? $json['address'] : null;
        $city = isset($json['city']) ? $json['city'] : null;
        $state = isset($json['state']) ? $json['state'] : null;
        $zip = isset($json['zip']) ? $json['zip'] : null;
        $residence = $json['residence'];
        \Utils::checkNotExists("Select * from Members where email = '" . $email . "';",
            "Email address already in use");
        $stmt = $con->prepare(
            "Insert into Members (first,last,email,password,token,address,city,state,zip,residence) values (?,?,?,?,?,?,?,?,?,?);");
        $stmt->bind_param('sssssssssi', $first, $last, $email, $password, $token, $address, $city, $state, $zip, $residence);
        $stmt->execute();
        \Utils::closeConnection($con);
    }
}

?>