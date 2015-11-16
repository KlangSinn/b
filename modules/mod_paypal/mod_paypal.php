<?php

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		no direct access
defined('_JEXEC') or die('Go Away');

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		API KEY?
// 		Security stuff

if(isset($_POST["api_key"], $_POST["api_pw"])) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select($db->quoteName(array('key', 'pw')));
	$query->from($db->quoteName('#__api_creds'));
	$query->where($db->quoteName('key') . ' LIKE '. $db->quote($_POST["api_key"]));
	$db->setQuery($query);
	$results = $db->loadObjectList();

	if(count($results) == 1) {
  		if($results[0]->pw != $_POST["api_pw"]) {
  			die("Authentication failed.2");
  		}
	} else {
		die("Authentication failed.1");
	}
}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

include "mod_paypalCredentials.php";

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		parameters from joomla backend
// 		needed for paypal request headers

$paypal_user       	= $params->get('paypal_user');
$paypal_pw       	= $params->get('paypal_pw');
$paypal_signature   = $params->get('paypal_signature');
$paypal_currencycode= $params->get('paypal_currencycode');
$paypal_returnurl   = $params->get('paypal_returnurl');
$paypal_cancelurl   = $params->get('paypal_cancelurl');

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		Parameters get from mobile device

if(isset($_POST["AMOUNT"], $_POST["EMAIL"], $_POST["DESC"])) {
	$paypal_amount = floatval($_POST["AMOUNT"]);
	$paypal_email = $_POST["EMAIL"];
	$paypal_desc = $_POST["DESC"];
} else {
	die("Missing parameters.");
}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		store and format GET parameter for paypal request
// 		use getRequestString() to get the parameters for the URL

$PC = new PaypalCredentials("SetExpressCheckout");
$PC->setCancelUrl($paypalcancel);
$PC->setReturnUrl($paypalreturn);
$PC->setUser($paypal_user);
$PC->setPw($paypal_pw);
$PC->setSignature($paypal_signature);
$PC->setCurrency($paypal_currencycode);
$PC->setReturnUrl($paypal_returnurl);
$PC->setCancelUrl($paypal_cancelurl);
$PC->setAmount($paypal_amount);
$PC->setPayerMail($paypal_email);
$PC->setDesc($paypal_desc);

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		Make cURL request to get TOKEN of transaction
// 		use paypal setExpressCheckout
// 		decode returned information
// 		store decoded information in array "parameter"

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

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// 		if transcation fails show error message
// 		else redirect to paypal

if($parameter["ACK"] == "Success" AND isset($parameter["TOKEN"])) {	
	header("Location: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=".$parameter["TOKEN"]/*."&useraction=commit"*/);
} else {
	print_r($parameter);
}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

?>