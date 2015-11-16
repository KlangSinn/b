<?php

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		no direct access
defined('_JEXEC') or die('Go Away');

include __DIR__ . "/../mod_paypal/mod_paypalCredentials.php";

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// ADD CSS STYLE TO THE PAGE

$document = JFactory::getDocument();
$document->addStyleSheet("modules/mod_paysuccess/style.css");

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

if(isset($_GET["submit"], $_GET["token"], $_GET["PayerID"])) {

	// 		after confirmen do payment on paypal	

	$token = $_GET["token"];
	$payerid = $_GET["PayerID"];

	$PC = new PaypalCredentials("DoExpressCheckoutPayment");
	$PC->setToken($token);
	$PC->setPayerId($payerid);

	$parameter = array();
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => 'https://api-3t.sandbox.paypal.com/nvp/?'.$PC->getRequestString(),
	    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	foreach (explode('&', curl_exec($curl)) as $chunk) {
	    $param = explode("=", $chunk);
	    if ($param) {
	    	$parameter = array_merge($parameter, array(urldecode($param[0]) => urldecode($param[1])));
	    }
	}
	curl_close($curl);

	if($parameter["ACK"] == "Success") {
		echo "<h3>Vielen Dank für Ihre Bestellung!</h3>";
	} else {
		echo "Fehler beim Abschließen Ihrer Bestellung!";
	}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

} else if(isset($_GET["token"], $_GET["PayerID"])) {

	// 		get information about payment and payer
	//  	show button to confirm payment and reload page	

	$token = $_GET["token"];
	$payerid = $_GET["PayerID"];
	$PC = new PaypalCredentials("GetExpressCheckoutDetails");
	$PC->setToken($token);

	$parameter = array();
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => 'https://api-3t.sandbox.paypal.com/nvp/?'.$PC->getRequestString(),
	    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	foreach (explode('&', curl_exec($curl)) as $chunk) {
	    $param = explode("=", $chunk);
	    if ($param) {
	    	$parameter = array_merge($parameter, array(urldecode($param[0]) => urldecode($param[1])));
	    }
	}
	curl_close($curl);

	if($parameter["ACK"] == "Success") {
		echo "Bestellung bestätigen.";
		echo "<h3>Ihre Bestellinformationen auf einen Blick</h3>";
		echo "<table>";
		echo "<tr><td><b>Name:</b></td><td>" . $parameter["PAYMENTREQUEST_0_SHIPTONAME"] . "</td></tr>";
		echo "<tr><td><b>E-Mail:</b></td><td>" . $parameter["EMAIL"] . "</td></tr>";
		echo "<tr><td><b>Betrag:</b></td><td>" . $parameter["AMT"] . " " . $parameter["CURRENCYCODE"] ."</td></tr>";
		echo "</table>";
		echo "<br><br>";
		echo '<a href="index.php/auftrag-gesendet?submit=true&token='.$token.'&PayerID='.$payerid.'" id="sendButton">Bestellung abschließen</a>';

		//  		insert information into database

		insertIntoDb($parameter);

	} else {
		echo "Fehler. Bei Ihrer Bestellung ist ein Fehler aufgetreten.";
	}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

} else {
	echo "Fehler. (Unknown TOKEN and PAYERID)";
}

function insertIntoDb($paras) {

}

?>