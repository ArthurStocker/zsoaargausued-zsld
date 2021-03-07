<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "PUT, POST, OPTIONS");


require_once 'class/Rest.php';
require_once 'class/UserRegAndAuth.php';

$api = new Rest();
$users = new UserRegAndAuth();


$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
	case 'POST':
		/**
		 * allow only if AUTHenticatied user
		 */
		if ( DeviceTAC::read( 'auth' ) ) {
			if (array_key_exists('type', $_GET) && isset($_GET['type']) && $_GET['type'] == 'access' && array_key_exists('object', $_GET) && isset($_GET['object']) && $_GET['object'] == 'user') {
				$data = file_get_contents("php://input");
				$transaction = $api->create($data, (string)$_GET['type'], (string)constant("DATASTORE_" . strtoupper($_GET['type'])), json_decode($data)->id);
			}
		} else {
			$users->init();
			if (array_key_exists('type', $_GET) && isset($_GET['type']) && $_GET['type'] == 'access' && array_key_exists('object', $_GET) && isset($_GET['object']) && $_GET['object'] == 'user') {
				$data = file_get_contents("php://input");
				$transaction = $users->registration($_GET, $data);
			}
		}

		if (isset($transaction)) {
			if ($transaction['errno'] === 409) { 
				header("HTTP/1.0 409 Conflict");
			} elseif ($transaction['errno'] !== 0) { 
				header("HTTP/1.0 422 Unprocessable Entity");
			} else {
				header("HTTP/1.0 201 Created");
			}
			header('Content-Type: application/json');
			echo json_encode($transaction, JSON_PRETTY_PRINT);
		} else {
			header("HTTP/1.0 404 Not Found");
		}
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}
?>
