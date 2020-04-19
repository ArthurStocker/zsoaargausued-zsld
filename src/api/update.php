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