<?php
include_once('../config/zsldtac.php');

ZSLDTAC::build(TRUE);

// array holding allowed Origin domains
$allowedOrigins = array(
    '(http(s)://)?(www\.)?zso-aargausued\.ch',
	'(http(s)://)?(www\.)?codepen\.io',
	'(http(s)://)?(www\.)?cdpn\.io',
);
   
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
	foreach ($allowedOrigins as $allowedOrigin) {
		if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
			header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 1000');
			header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
			header('Access-Control-Allow-Credentials: true');
			break;
		}
	}
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Rest.php');
$api = new Rest();


switch($requestMethod) {
	case 'GET':
		if (array_key_exists('move', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('move', (string)$_GET['move'], (string)constant("DATASTORE_" . strtoupper($_GET['move'])), (int)$_GET['id']);
		} elseif (array_key_exists('park', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('park', (string)$_GET['park'], (string)constant("DATASTORE_" . strtoupper($_GET['park'])), (int)$_GET['id']);
		} elseif (array_key_exists('here', $_GET) && isset($_GET['id'])) {
			$transaction = $api->create('here', (string)$_GET['here'], (string)constant("DATASTORE_" . strtoupper($_GET['here'])), (int)$_GET['id'], true);
		//} elseif (array_key_exists('show', $_GET) && isset($_GET['show']) && isset($_GET['id'])) {
		//	$api->read($_GET, 'transaction', (string)constant("DATASTORE_" . strtoupper($_GET['show'])), (int)$_GET['id']);
		} else {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		if (isset($transaction)) {
			header("Location: https://" . $_SERVER['HTTP_HOST'] . "/map/?tic=" . $transaction['tic'] . "&data=" . $transaction['data'] . "&type=" . $transaction['type'] . "&id=" . $transaction['id'] . "&valid=" . $transaction['valid'] . "&errno=" . $transaction['errno'] . "&error=" . $transaction['error'], true, 303);
		}
		break;
	case 'POST':
		if (array_key_exists('here', $_GET) && isset($_GET['id']) && defined("DEVICE_TAC")) {
			$transaction = $api->create(file_get_contents("php://input"), (string)$_GET['here'], (string)constant("DATASTORE_" . strtoupper($_GET['here'])), (int)$_GET['id'], true);
		} elseif (array_key_exists('register', $_GET) && defined("DEVICE_TAC")) {
			$transaction = $api->create(file_get_contents("php://input"), (string)$_GET['register'], (string)constant("DATASTORE_" . strtoupper($_GET['register'])), constant("DEVICE_TAC"));
		}
		if (isset($transaction)) {
			echo json_encode($transaction, JSON_PRETTY_PRINT);
			if ($transaction['errno'] === 409) { 
				header("HTTP/1.0 409 Conflict");
			} elseif ($transaction['errno'] !== 0) { 
				header("HTTP/1.0 422 Unprocessable Entity");
			} else {
				header("HTTP/1.0 201 Created");
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
