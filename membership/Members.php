<?php

namespace membership;

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Stripe.php';

class Members {

    /*
     * Obtains Member objects.
     */
    public static function getMembers() {
        exit(\Utils::getJSONObjects("Select * from Members"));
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
				"email" => urlencode($json['email']),
				"name" => $json['first_name'] . $json['last_name'],
				"residence" => $json['residence'],
				"unit" => $json['residence-unit'],
				"quantity" => $json['purchaseQuantity'])
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
        $address = $json['address'];
        $city = $json['city'];
        $state = $json['state'];
        $zip = $json['zip'];
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