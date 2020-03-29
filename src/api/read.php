<?php
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
			break;
		}
	}
}


$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Rest.php');
$api = new Rest();
switch($requestMethod) {
	case 'GET':
		//print_r($_GET);
		$api->getObjects((string)$_GET['type'], (string)$_GET['object'], (int)$_GET['id']);
		break;
	default:
		header("HTTP/1.0 405 method not allowed");
		break;
}
?>