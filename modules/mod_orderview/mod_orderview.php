<?php

// no direct access
defined('_JEXEC') or die('Go Away');

// // // // // // // // // // // // // // // // // // // // // // // // 

// GET PARAMETER FROM JOOMLA EDITOR

//$moduleclass_sfx       = $params->get('moduleclass_sfx');

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
$query->select($db->quoteName(array('order_id', 'payer_first_name', 'payer_last_name', 'ship_to_zip', 'ship_to_street', 'ship_to_city', 'amount', 'payer_email', 'success', 'desc')));
$query->from($db->quoteName('#__product_orders'));
//$query->where($db->quoteName('profile_key') . ' LIKE '. $db->quote('\'custom.%\''));
//$query->order('ordering ASC');
 
// Reset the query using our newly populated query object.
$db->setQuery($query);
 
// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();

// // // // // // // // // // // // // // // // // // // // // // // // 

// PRINT LIST OF ORDERS

//echo "<div class=\"orderView " . $moduleclass_sfx ."\">";
echo "<div class=\"orderView\">";
echo "<table>";
echo "<tr><td>ID</td><td>Name</td><td>E-Mail</td><td>Adresse</td><td>Preis</td><td>Abgeschlossen?</td><td>Beschreibung</td></tr>";
foreach($results as &$record) {
  echo "<tr>";
  echo "<td>" . $record->order_id . "</td>";

  // user
  echo "<td>" . $record->payer_last_name . ", " . $record->payer_first_name . "</td>";
  echo "<td>" . $record->payer_email . "</td>";  

  // address
  echo "<td>" . $record->ship_to_street . ", <br>" . $record->ship_to_zip . " " . $record->ship_to_city . "</td>";

  // product
  echo "<td>" . $record->amount . " EUR</td>";

  echo "<td>" . ($record->success == 1 ? "<span style=\"color: green;\">JA</span>" : "<span style=\"color: red;\">NEIN</span>") . "</td>";

  echo "<td>" . $record->desc . "</td>";    

  echo "</tr>";
}
echo "</table>";
echo "</div>";
?>