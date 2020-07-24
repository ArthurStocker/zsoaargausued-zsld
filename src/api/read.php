<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, OPTIONS");


require_once 'class/Rest.php';

$api = new Rest();


$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
	case 'GET':
		if ( isset($_GET['type']) ) {
			if ( isset($_GET['object']) && ( DeviceTAC::read( 'auth' ) || ( (string)$_GET['object'] !== 'devices' && (string)$_GET['object'] !== 'users' ) ) ) {
				if ( isset($_GET['id']) ) {
					$transaction = $api->read($_GET, (string)$_GET['type'], (string)$_GET['object'], (int)$_GET['id']);
				} else {
					$transaction = $api->read($_GET, (string)$_GET['type'], (string)$_GET['object'], 0);
				}
			} else {
				$transaction = $api->read($_GET, (string)$_GET['type'], '', 0);
			}
			if (isset($transaction)) {
				header('Content-Type: application/json');
				echo json_encode($transaction, JSON_PRETTY_PRINT);
			} else {
				header("HTTP/1.0 404 Not Found");
			}
		} else {
			header("HTTP/1.0 404 Not Found");
		}
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}
?>