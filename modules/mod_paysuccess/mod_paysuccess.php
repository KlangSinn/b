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

// 		parameters from joomla backend
// 		needed for paypal request headers

$paypal_user       	= $params->get('paypal_user');
$paypal_pw       	= $params->get('paypal_pw');
$paypal_signature   = $params->get('paypal_signature');
$paypal_currencycode= $params->get('paypal_currencycode');

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// ADD CSS STYLE TO THE PAGE

$document = JFactory::getDocument();
$document->addStyleSheet("modules/mod_paysuccess/style.css");

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

if(isset($_GET["submit"], $_GET["token"], $_GET["PayerID"], $_GET["amount"], $_GET["order_id"])) {

	// 		after confirmen do payment on paypal	

	$token 			= $_GET["token"];
	$payerid 		= $_GET["PayerID"];
	$paypal_amount 	= floatval($_GET["amount"]);
	$order_id 		= $_GET["order_id"];

	$PC = new PaypalCredentials("DoExpressCheckoutPayment");
	$PC->setToken($token);
	$PC->setPayerId($payerid);
	$PC->setUser($paypal_user);
	$PC->setPw($paypal_pw);
	$PC->setSignature($paypal_signature);
	$PC->setCurrency($paypal_currencycode);
	$PC->setAmount($paypal_amount);

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
		if(updateInDb("order_id", $order_id, "success", 1, "#__product_orders")) {
			echo "<h3>Vielen Dank für Ihre Bestellung!</h3>";
			$document = JFactory::getDocument();
			$document->addScriptDeclaration('
				jQuery(document).ready(function(){
					var toast = "Auftrag erfolgreich ausgeführt.";
					Android.showToast(toast);
				});	
			');
		} else {
			echo "Fehler beim Abschließen Ihrer Bestellung! (Update failed)";	
		}
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
	$PC->setUser($paypal_user);
	$PC->setPw($paypal_pw);
	$PC->setSignature($paypal_signature);

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

		//  	insert information into database
		$order_id = insertIntoDb($parameter);

		echo "Bestellung bestätigen.";
		echo "<h3>Ihre Bestellinformationen auf einen Blick</h3>";
		echo "<table>";
		echo "<tr><td><b>Name:</b></td><td>" . $parameter["PAYMENTREQUEST_0_SHIPTONAME"] . "</td></tr>";
		echo "<tr><td><b>E-Mail:</b></td><td>" . $parameter["EMAIL"] . "</td></tr>";
		echo "<tr><td><b>Bestellnummer:</b></td><td>" . $order_id . "</td></tr>";
		echo "<tr><td><b>Beschreibung:</b></td><td>" . $parameter["DESC"] . "</td></tr>";
		echo "<tr><td><b>Sendeadresse:</b></td><td>" . $parameter["SHIPTONAME"] . "<br>" . $parameter["SHIPTOSTREET"] . "<br>" . $parameter["SHIPTOZIP"] ." ". $parameter["SHIPTOCITY"] ."</td></tr>";
		echo "<tr><td><b>Betrag:</b></td><td>" . $parameter["AMT"] . " " . $parameter["CURRENCYCODE"] ."</td></tr>";
		echo "</table>";
		echo "<br><br>";
		echo '<a href="index.php/auftrag-gesendet?submit=true&token='.$token.'&PayerID='.$payerid.'&amount='.$parameter["AMT"].'&order_id='.$order_id.'" id="sendButton">Bestellung abschließen</a>';
	} else {
		echo "Fehler. Bei Ihrer Bestellung ist ein Fehler aufgetreten.";
		//print_r($parameter);
	}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

} else {
	echo "Fehler. (Unknown TOKEN and PAYERID)";
}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

function insertIntoDb($paras) {
	$payer_first_name 	= $paras["FIRSTNAME"];
	$payer_last_name 	= $paras["LASTNAME"];
	$payer_email  		= $paras["EMAIL"];
	$ship_to_name  		= $paras["SHIPTONAME"];
	$ship_to_street  	= $paras["SHIPTOSTREET"];
	$ship_to_city  		= $paras["SHIPTOCITY"];
	$ship_to_zip  		= $paras["SHIPTOZIP"];
	$amount   			= $paras["AMT"];
	$timestamp  		= $paras["TIMESTAMP"];
	$desc 				= $paras["DESC"];
	$db = JFactory::getDbo();	 
	$query = $db->getQuery(true);	 
	$columns = array(	
						'payer_first_name', 
						'payer_last_name', 
						'payer_email', 
						'ship_to_name', 
						'ship_to_street', 
						'ship_to_city', 
						'ship_to_zip', 
						'amount', 
						'timestamp', 
						'desc', 
						'success'
					);
	$values = array(
						$db->quote($payer_first_name),
						$db->quote($payer_last_name),
						$db->quote($payer_email),
						$db->quote($ship_to_name),
						$db->quote($ship_to_street),
						$db->quote($ship_to_city),
						$db->quote($ship_to_zip),
						$amount,
						$db->quote($timestamp),
						$db->quote($desc),
						0
					);
	$query
	    ->insert($db->quoteName('#__product_orders'))
	    ->columns($db->quoteName($columns))
	    ->values(implode(',', $values));
	$db->setQuery($query);
	$db->execute();
	return $db->insertid();
}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

function updateInDb($idkey, $idvalue, $key, $value, $table) {
	$db = JFactory::getDbo();	 
	$query = $db->getQuery(true);	
	$fields = array(
	    $db->quoteName($key) . ' = ' . $value
	);	 
	$conditions = array(
	    $db->quoteName($idkey) . ' = ' . $idvalue
	);
	$query->update($db->quoteName($table))->set($fields)->where($conditions);	 
	$db->setQuery($query);	 
	$result = $db->execute();	
	return $result;	
}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

?>