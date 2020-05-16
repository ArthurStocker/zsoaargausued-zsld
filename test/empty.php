<?php
$response = new stdClass();
$response->type = 'RegisteredObjects';
$response->objects = [];
define("REGISTERED_DEVICE", (int)!empty($response->objects));
echo '-->' . (int)!empty($response->objects) . "<--\n";
echo '-->' . REGISTERED_DEVICE . "<--\n";
$device = new stdClass();
$device->force = true;
$force = false;
echo '-->' . ($force || $device->force) . "<--\n";
?>