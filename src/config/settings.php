<?php

define("SYSTEM", "public");
define("SETTINGS", "/../../../../../../Desktop/ZSO/settings.xlsx");

require_once __DIR__.'/../class/SimpleXLSX.php';

if ($xlsx = SimpleXLSX::parse(__DIR__ . SETTINGS)) {
    // Produce array keys from the array values of 1st array element
    $fields = $rows = [];

    foreach ( $xlsx->rows(1) as $k => $r ) {
        if ( $k === 0 ) {
            $fields = $r;
            continue;
        }
        $values = array_combine( $fields, $r );
        if ($values['active'] === 'true' && ($values['system'] === 'all' || $values['system'] === SYSTEM) ) {
            define($values['key'], $values['value']);
        }
    }
} else {
    define("ERROR", SimpleXLSX::parseError());
}
?>