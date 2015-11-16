<?php

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

// no direct access
defined('_JEXEC') or die('Go Away');

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
	header("Location: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=".$parameter["TOKEN"]);
} else {
	print_r($parameter);
}

// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //
// // // // // // // // // // // // // // // // // // // //

?>