<?php
require_once "start.php";

$db = \library\database\DataBase::getDBO();

$select = new \library\database\SelectDB();
$select->from('articles', array('*'));
$data = $db->selectRow($select);
print_r($data);
\core\Route::route();
?>