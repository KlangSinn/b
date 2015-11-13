<?php

// no direct access
defined('_JEXEC') or die('Go Away');


$db = JFactory::getDbo();
 
$query = $db->getQuery(true);

$successCode = 1;
 
// Fields to update.
$fields = array(
    $db->quoteName('success') . ' = ' . $successCode
);
 
// Conditions for which records should be updated.
$conditions = array(
    $db->quoteName('orderId') . ' = ' . $orderId
);
 
$query->update($db->quoteName('#__product_orders'))->set($fields)->where($conditions);
 
$db->setQuery($query);
 
//$result = $db->execute();

echo "Vielen Dank für Ihre Bestellung!<br>Auftragsnummer: " . $orderId;

$document = JFactory::getDocument();
$document->addScriptDeclaration('
	jQuery(document).ready(function(){
		var toast = "Auftrag erfolgreich ausgeführt.";
		Android.showToast(toast);
	});	
');

?>