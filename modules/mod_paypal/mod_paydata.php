<?php

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

// GET PARAMETERS OF ORDER FROM APP
// POST OR GET?
$firstName 		= "Max";
$lastName 		= "Mustermann";
$postCode 		= "99510";
$street 		= "Lindwurmweg";
$houseNumber 	= "6";
$city 			= "Weimar";
$amount 		= "15";
$productId 		= "16";
$email 			= "max.mustermann@email.de";

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

// INSERT ORDER INTO DATABASE
// Get a db connection.
$db = JFactory::getDbo();
 
// Create a new query object.
$query = $db->getQuery(true);
 
// Insert columns.
$columns = array('firstName', 'lastName', 'postCode', 'street', 'houseNumber', 'city', 'amount', 'productId', 'email');
 
// Insert values.
$values = array( 
	$db->quote($firstName), 
	 $db->quote($lastName), 
	  $postCode, 
	   $db->quote($street), 
	    $houseNumber, 
	     $db->quote($city), 
	      $amount, 
	       $productId,
	       $db->quote($email)
	);
 
// Prepare the insert query.
$query
    ->insert($db->quoteName('#__product_orders'))
    ->columns($db->quoteName($columns))
    ->values(implode(',', $values));
 
// Set the query using our newly populated query object and execute it.
$db->setQuery($query);
$db->execute();

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

// SET PARAMETERS FOR FORWARING
$amount = (int) $amount;		// price of order
$currency_code = trim('EUR'); 	// standard currency

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

?>