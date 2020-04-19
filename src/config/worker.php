<script>
/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * Die Konfigurationswerte werden durch das config/settings.php geladen
 */
// Service Worker
var SW_REGISTRATION;
if ('serviceWorker' in navigator && 'PushManager' in window) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('<?php echo WORKER;?>')
        .then(registration => {
            console.log('Service Worker registered with scope:', registration.scope);
            SW_REGISTRATION = registration;
        })
        .catch(err => {
            console.error('Service Worker registration failed:', err);
        });
    });
} else {
    console.warn('Service Worker or Push Manager is not supported!');
}

<?php
    echo "\nvar QUERY_STRING = " . json_encode($_GET, JSON_PRETTY_PRINT) . ";\n\n";

    if ($devices = ObjectStore::parse(__DIR__ . '/' . DATA_PATH . DATASTORE_DEVICE . '.json')) {
        $data = ObjectStore::build( $devices->list("0") );
        define("REGISTERED_DEVICE", (int)!empty($data->devices));
        echo "\nvar REGISTERED_DEVICE = " . (int)!empty($data->devices) . ";\n";
        //echo "var DEBUG_REGISTERED_DEVICE = " . json_encode($data, JSON_PRETTY_PRINT) . ";\n";
    } else {
        define("REGISTERED_DEVICE", -1);
        define("REGISTRATION_ERROR", ObjectStore::parseError());
        echo "\nvar REGISTERED_DEVICE = -1;\n";
        echo "var REGISTRATION_ERROR = " . ObjectStore::parseError() . ";\n";
    }

    $key = '';
    if ( array_key_exists('REGISTRATIONDIALOG', $_GET) ) {
        if ($key = file_get_contents(__DIR__ . '/' . DATA_PATH  . 'registration-popup-key.txt')) {
            if ( $_GET['REGISTRATIONDIALOG'] !== $key ) {
                $key = '';
            }
        }
    }
    echo "var REGISTRATIONDIALOG = '" . $key . "';\n";
    
    if ($xlsx = SimpleXLSX::parse(__DIR__ . SETTINGS)) {
    // Produce array keys from the array values of 1st array element
    $fields = $rows = [];

    foreach ( $xlsx->rows(0) as $k => $r ) {
        if ( $k === 0 ) {
            $fields = $r;
            continue;
        }
        if ( $k === 1 ) {
            $values = array_combine( $fields, $r );
            echo "// " . $values['§Institution'] . "\n";
            echo "var MAP_CENTER_X = " . $values['KoordinateX'] . ";\n";
            echo "var MAP_CENTER_Y = " . $values['KoordinateY'] . ";\n\n";
        }
    }

    foreach ( $xlsx->rows(1) as $k => $r ) {
        if ( $k === 0 ) {
            $fields = $r;
            continue;
        }
        $values = array_combine( $fields, $r );
        if ($values['active'] === 'true' && $values['hide'] === 'false' && ($values['system'] === 'all' || $values['system'] === SYSTEM) ) {
            echo "// " . $values['description'] . "\n";
            if ($values['type'] === 'string') {
                $string = "'";
            } else {
                $string = "";
            }
            echo "var " . $values['key'] . " = " . $string . $values['value'] . $string . ";\n\n";
            }
        }
    } else {
    $data = SimpleXLSX::parseError();
    }
?>
</script>
<script src="/map/class/Rest.js"></script>
<script src="/map/lib/helper.js"></script>