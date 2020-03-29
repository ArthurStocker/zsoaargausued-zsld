<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Rest.php');
$api = new Rest();
switch($requestMethod) {	
	case 'PUT':
		parse_str(file_get_contents("php://input"),$v_PUT);
		print_r($v_PUT);
		$api->updateObject($v_PUT);
		break;
	default:
		header("HTTP/1.0 405 method not allowed");
		break;
}
?>