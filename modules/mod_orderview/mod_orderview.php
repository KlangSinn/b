<?php

// no direct access
defined('_JEXEC') or die('Go Away');

// // // // // // // // // // // // // // // // // // // // // // // // 

// GET PARAMETER FROM JOOMLA EDITOR

$moduleclass_sfx       = $params->get('moduleclass_sfx');

// // // // // // // // // // // // // // // // // // // // // // // // 

// ADD CSS STYLE TO THE PAGE

$document = JFactory::getDocument();
$document->addStyleSheet("modules/mod_orderview/style.css");

// // // // // // // // // // // // // // // // // // // // // // // // 

// GET ORDERS FROM DATABASE

// Get a db connection.
$db = JFactory::getDbo();
 
// Create a new query object.
$query = $db->getQuery(true);
 
// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
$query->select($db->quoteName(array('orderId', 'firstName', 'lastName', 'postCode', 'street', 'houseNumber', 'city', 'amount', 'productId', 'email')));
$query->from($db->quoteName('#__product_orders'));
//$query->where($db->quoteName('profile_key') . ' LIKE '. $db->quote('\'custom.%\''));
//$query->order('ordering ASC');
 
// Reset the query using our newly populated query object.
$db->setQuery($query);
 
// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();

// // // // // // // // // // // // // // // // // // // // // // // // 

// PRINT LIST OF ORDERS

echo "<div class=\"orderView " . $moduleclass_sfx ."\">";
echo "<table>";
foreach($results as &$record) {
  echo "<tr>";
  echo "<td>" . $record->orderId . "</td>";

  // user
  echo "<td>" . $record->lastName . ", " . $record->firstName . "</td>";
  echo "<td>" . $record->email . "</td>";  

  // address
  echo "<td>" . $record->street . " " . $record->houseNumber . ", " . $record->postCode . " " . $record->city . "</td>";

  // product
  echo "<td>" . $record->amount . " EUR</td>";
  echo "<td>" . $record->productId . "</td>";    
  echo "</tr>";
}
echo "</table>";
echo "</div>";
?>