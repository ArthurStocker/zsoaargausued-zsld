<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, PUT, POST, DELETE, OPTIONS");


require_once 'class/Rest.php';

$api = new Rest();


$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
	case 'GET':
		if ( json_decode( DeviceTAC::read( 'person', true ) )->display === "unbekannt" ) {
			DeviceTAC::redirect("/map/registration", "redirect=" . DeviceTAC::redirect()->path, true);
			echo "";
			exit;
		}
		if (array_key_exists('move', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('move', (string)$_GET['move'], (string)constant("DATASTORE_" . strtoupper($_GET['move'])), (int)$_GET['id']);
		} elseif (array_key_exists('park', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('park', (string)$_GET['park'], (string)constant("DATASTORE_" . strtoupper($_GET['park'])), (int)$_GET['id']);
		} elseif (array_key_exists('here', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('{ "id":0, "display": "registration", "properties": "non","concurrentobjectsallowed": true }', (string)$_GET['here'], (string)constant("DATASTORE_" . strtoupper($_GET['here'])), (int)$_GET['id'], true);
			exit;
		} else {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		if (isset($transaction)) {
			header("Location: https://" . $_SERVER['HTTP_HOST'] . "/map/odometer?tic=" . $transaction['tic'] . "&data=" . $transaction['data'] . "&type=" . $transaction['type'] . "&id=" . $transaction['id'] . "&valid=" . $transaction['valid'] . "&errno=" . $transaction['errno'] . "&error=" . $transaction['error'], true, 303);
		}
		break;
	case 'POST':
		if (array_key_exists('here', $_GET) && isset($_GET['id'])) {
			$data = file_get_contents("php://input");
			$transaction = $api->create($data , (string)$_GET['here'], (string)constant("DATASTORE_" . strtoupper($_GET['here'])), $_GET['id'], true);
		} elseif (array_key_exists('register', $_GET)) {
			$data = file_get_contents("php://input");
			//$transaction = $api->update($data , (string)$_GET['register'], (string)constant("DATASTORE_" . strtoupper($_GET['register'])), json_decode($data)->id);
			if ( !file_put_contents( DATA_PATH  . 'debug_200.txt', json_encode( $data, JSON_PRETTY_PRINT ) ) ) {
			}
			if ( !file_put_contents( DATA_PATH  . 'debug_210.txt', json_encode( session_status(), JSON_PRETTY_PRINT ) ) ) {
			}
			if ( !file_put_contents( DATA_PATH  . 'debug_211.txt', json_encode( session_id(), JSON_PRETTY_PRINT ) ) ) {
			}
			DeviceTAC::restore();
			if ( !file_put_contents( DATA_PATH  . 'debug_220.txt', json_encode( session_status(), JSON_PRETTY_PRINT ) ) ) {
			}
			if ( !file_put_contents( DATA_PATH  . 'debug_221.txt', json_encode( session_id(), JSON_PRETTY_PRINT ) ) ) {
			}
			DeviceTAC::write( 'person', $data );
			DeviceTAC::commit();
			if ( !file_put_contents( DATA_PATH  . 'debug_230.txt', json_encode( $_SESSION, JSON_PRETTY_PRINT ) ) ) {
			}
            $transaction = json_decode( $data );
		}
		if (isset($transaction)) {
			if ($transaction['errno'] === 409) { 
				header("HTTP/1.0 409 Conflict");
			} elseif ($transaction['errno'] !== 0) { 
				header("HTTP/1.0 422 Unprocessable Entity");
			} else {
				header("HTTP/1.0 201 Created");
				header('Content-Type: application/json');
			}
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
