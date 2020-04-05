<?php
chdir('../src/api/');
include('../class/Rest.php');
$r = new Rest();
$r->getObjects('system', 'settings', 3);
?>