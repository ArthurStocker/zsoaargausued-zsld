<?php
chdir('../src/api/');
include('../class/Rest.php');
$r = new Rest();
$r->read('system', 'settings', 3);
?>