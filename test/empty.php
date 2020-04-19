<?php
$response = new stdClass();
$response->type = 'RegisteredDevices';
$response->devices = [];
define("REGISTERED_DEVICE", (int)!empty($response->devices));
echo '-->' . (int)!empty($response->devices) . "<--\n";
echo '-->' . REGISTERED_DEVICE . "<--\n";
$device = new stdClass();
$device->force = true;
$force = false;
echo '-->' . ($force || $device->force) . "<--\n";
?>