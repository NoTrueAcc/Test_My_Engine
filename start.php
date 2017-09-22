<?php
	mb_internal_encoding('UTF-8');
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once 'autoloader/Autoloader.php';
	\autoloader\Autoloader::register();

	define('MAIN_MENU', 1);
	define('TOP_MENU', 2);
	define('KB_B', 1024);
	define('PAY_COURSE', 1);
	define('FREE_COURSE', 2);
	define('ONLINE_COURSE', 3);

	\core\database\AbstractObjectDB::setDB(\library\database\DataBase::getDBO());
?>