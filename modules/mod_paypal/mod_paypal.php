<?php

// no direct access
defined('_JEXEC') or die('Go Away');

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

// PARAMETERS 
// from joomla editor

$paypal_email       = $params->get('paypal_email');
$paypal_org         = $params->get('paypal_org');
$paypalcancel       = $params->get('paypalcancel');
$paypalreturn       = $params->get('paypalreturn');
$paymentlocation   	= $params->get('paymentlocation');

if(isset(
		$_POST["firstName"], 
		$_POST["lastName"], 
		$_POST["postCode"],
		$_POST["street"],
		$_POST["houseNumber"],
		$_POST["city"],
		$_POST["amount"],
		$_POST["productId"],
		$_POST["email"])) {	
	$firstName 		= $_POST["firstName"];
	$lastName 		= $_POST["lastName"];
	$postCode 		= $_POST["postCode"];
	$street 		= $_POST["street"];
	$houseNumber 	= $_POST["houseNumber"];
	$city 			= $_POST["city"];
	$amount 		= $_POST["amount"];
	$productId 		= $_POST["productId"];
	$email 			= $_POST["email"];
} else {
	$error = true;
}

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

if(!$error) {	

	//	BLUMENDO EDIT
	// 	access data from app
	//	edit parameters to forwarding to paypal
	// 	insert order in own database
	//	after execution go to cancel or success page

	include "mod_paydata.php";

	// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

	// REDIRECT TO PAYPAL
	// for other payment types the url has to be changed

	$header = "Location: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=".$paypal_email."&item_name=".$paypal_org."&amount=".$amount."&no_shipping=0&no_note=1&tax=0&currency_code=".$currency_code."&bn=PP%2dBuyNowBF&charset=UTF%2d8&return=".$paypalreturn."&cancel=".$paypalcancel;
	//$header = "Location: http://saschadobschal.de/blumendoTest/index.php/auftrag-gesendet?recordId=" . $recordId;

	if ($paymentlocation != "") {
		$header = $header."&lc=".$paymentlocation;
	}	

	header($header);
} else {
	echo "<span style=\"color: red; font-size: 150%;\">A error occurs.</span>";
}

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

?>