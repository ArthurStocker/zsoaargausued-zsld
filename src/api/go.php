<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, PUT, POST, DELETE, OPTIONS");


require_once 'class/Rest.php';

$api = new Rest();


$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
	case 'GET':
		if (array_key_exists('move', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('move', (string)$_GET['move'], (string)constant("DATASTORE_" . strtoupper($_GET['move'])), (int)$_GET['id']);
		} elseif (array_key_exists('park', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('park', (string)$_GET['park'], (string)constant("DATASTORE_" . strtoupper($_GET['park'])), (int)$_GET['id']);
		} elseif (array_key_exists('here', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('{ "display": "check-in" }', (string)$_GET['here'], (string)constant("DATASTORE_" . strtoupper($_GET['here'])), (int)$_GET['id'], true);
		//} elseif (array_key_exists('show', $_GET) && isset($_GET['show']) && isset($_GET['id'])) {
		//	$api->read($_GET, 'transaction', (string)constant("DATASTORE_" . strtoupper($_GET['show'])), (int)$_GET['id']);
		} else {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		if (isset($transaction)) {
			//header("Location: https://" . $_SERVER['HTTP_HOST'] . "/map/?tic=" . $transaction['tic'] . "&data=" . $transaction['data'] . "&type=" . $transaction['type'] . "&id=" . $transaction['id'] . "&valid=" . $transaction['valid'] . "&errno=" . $transaction['errno'] . "&error=" . $transaction['error'], true, 303);
			header("Location: https://" . $_SERVER['HTTP_HOST'] . "/map/odometer?tic=" . $transaction['tic'] . "&data=" . $transaction['data'] . "&type=" . $transaction['type'] . "&id=" . $transaction['id'] . "&valid=" . $transaction['valid'] . "&errno=" . $transaction['errno'] . "&error=" . $transaction['error'], true, 303);
		}
		break;
	case 'POST':
		if (array_key_exists('here', $_GET) && isset($_GET['id']) && defined("DEVICE_TAC")) {
			$data = file_get_contents("php://input");
			$transaction = $api->create($data , (string)$_GET['here'], (string)constant("DATASTORE_" . strtoupper($_GET['here'])), constant("DEVICE_TAC"), true);
		} elseif (array_key_exists('register', $_GET) && defined("DEVICE_TAC")) {
			$data = file_get_contents("php://input");
			$transaction = $api->update($data , (string)$_GET['register'], (string)constant("DATASTORE_" . strtoupper($_GET['register'])), constant("DEVICE_TAC"));
		}
		if (isset($transaction)) {
			echo json_encode($transaction, JSON_PRETTY_PRINT);
			if ($transaction['errno'] === 409) { 
				header("HTTP/1.0 409 Conflict");
			} elseif ($transaction['errno'] !== 0) { 
				header("HTTP/1.0 422 Unprocessable Entity");
			} else {
				header("HTTP/1.0 201 Created");
				header('Content-Type: application/json');
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
