<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Rest.php');
$api = new Rest();
switch($requestMethod) {
	case 'POST':
		print_r($_POST);
		$api->insertObject($_POST);
		break;
	default:
		header("HTTP/1.0 405 method not allowed");
		break;
}
?>