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
			$transaction = $api->update($_GET, (string)$_GET['type'], (string)$_GET['object'], 'NULL');
		} else {
			header("HTTP/1.0 404 Not Found");
		}
		if (isset($transaction)) {
			header('Content-Type: application/json');
			echo json_encode($transaction, JSON_PRETTY_PRINT);
		}
		break;
	case 'POST':
		/**
		 * allow only if AUTHenticatied user
		 */
		if ( DeviceTAC::read( 'auth' ) ) {
			if (array_key_exists('permissions', $_GET)) {
				$data = file_get_contents("php://input");
				$transaction = $api->update($data, (string)$_GET['permissions'], (string)constant("DATASTORE_" . strtoupper($_GET['permissions'])), json_decode($data)->id);
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
		} else {
			header("HTTP/1.1 403 Forbidden");
		}
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}
?>