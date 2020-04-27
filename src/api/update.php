<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, PUT, POST, DELETE, OPTIONS");


require_once 'class/Rest.php';

$api = new Rest();


$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
	case 'GET':
		if (isset($_GET['type']) && isset($_GET['object'])) {
			$api->update($_GET, (string)$_GET['type'], (string)$_GET['object']);
		} else {
			header("HTTP/1.0 404 Not Found");
		}
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}
?>