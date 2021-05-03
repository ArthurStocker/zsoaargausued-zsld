<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

define("SYSTEM", "public");
define("SETTINGS", "/../../../../../Desktop/ZSO/settings.xlsx"); # ../wp-content/uploads/settings.xlsx

require_once 'class/SimpleXLSX.php';

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

# (Manual  http://www.php.net/manual/de/session.configuration.php)
ini_set('session.gc_divisor', SESSION_GC_DIVISOR); # default 100 
ini_set('session.gc_probability', SESSION_GC_PROBABILITY); # default 1
ini_set('session.gc_maxlifetime', SESSION_GC_MAXLIFETIME); # default 1440 sec

require_once '../wp-content/uploads/sms_daten.php';

?>