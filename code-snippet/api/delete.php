<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Rest.php');
$api = new Rest();
switch($requestMethod) {
	case 'DELETE':
		parse_str(file_get_contents("php://input"),$v_DELETE);
		print_r($v_DELETE);
		$api->deleteEmployee($v_DELETE);
		break;
	default:
		header("HTTP/1.0 405 method not allowed");
		break;
}
?>