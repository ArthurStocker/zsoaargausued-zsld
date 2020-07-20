<?php
class UI {
    
    private $debug;

    function __construct($debug = false) {
        $this->debug = $debug;
    }

    public function init() { 
?>
<script>
<?php 
    if (DeviceTAC::isValid() && $objects = ObjectStore::parse(DATA_PATH . DATASTORE_DEVICE . '.json')) {
        $data = ObjectStore::build( $objects->list("0") );
        $registered = 0;
        if ( !empty($data->objects) && $data->objects[0]['data'] !== "unbekannt" )  {
            $registered = 1;
        }
        define("REGISTERED_DEVICE", $registered);
        echo "\nvar REGISTERED_DEVICE = " . $registered . ";\n";
        //echo "var DEBUG_REGISTERED_DEVICE = " . json_encode($data, JSON_PRETTY_PRINT) . ";\n";
    } else {
        define("REGISTERED_DEVICE", -2);
        define("REGISTRATION_ERROR", ObjectStore::parseError());
        echo "\nvar REGISTERED_DEVICE = -2;\n";
        echo "var REGISTRATION_ERROR = '" . ObjectStore::parseError() . "';\n";
    }

    echo "\nvar QUERY_STRING = " . json_encode($_GET, JSON_PRETTY_PRINT) . ";\n\n";

    echo "\nvar AUTH = " . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ";\n\n";

    if ($xlsx = SimpleXLSX::parse(SETTINGS)) {
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
<?php
    }
}
?>