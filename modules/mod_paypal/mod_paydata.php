<?php

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

// INSERT ORDER INTO DATABASE
// Get a db connection.
$db = JFactory::getDbo();
 
// Create a new query object.
$query = $db->getQuery(true);
 
// Insert columns.
$columns = array('firstName', 'lastName', 'postCode', 'street', 'houseNumber', 'city', 'amount', 'productId', 'email', 'error', 'success');
 
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
	       $db->quote($email),
	        0,
	         0
	);
 
// Prepare the insert query.
$query
    ->insert($db->quoteName('#__product_orders'))
    ->columns($db->quoteName($columns))
    ->values(implode(',', $values));
 
// Set the query using our newly populated query object and execute it.
$db->setQuery($query);
$db->execute();

$recordId = $db->insertid();

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 

// SET PARAMETERS FOR FORWARING
$amount = (int) $amount;		// price of order
$currency_code = trim('EUR'); 	// standard currency

// // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 


?>