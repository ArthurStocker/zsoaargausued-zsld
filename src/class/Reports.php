<?php
require_once 'class/ObjectStore.php';
require_once 'class/TransactionStore.php';

class Reports {
    
    private $debug;

    private function fahrer() {
        $api = new Rest();
        
        if ( isset($_GET['objectstore']) ) {
            $objectstore = $api->read(array("id" => -1), 'objectstore', (string)$_GET['objectstore'], -1);
            foreach ($objectstore->objects as $record) {
                $fahrzeug = $record["id"];
                $wert = $record["data"];
                if (array_key_exists("properties", $record) && array_key_exists("description", $record["properties"])) {
                    $action = $record["properties"]["description"];
                } else {
                    $action = "";
                }
                if (array_key_exists("person", $record) && array_key_exists("display", $record["person"])) {
                    $benutzer = $record["person"]["display"];
                } else {
                    $benutzer = "";
                }
                $datum = $record["valid"];
                $records[] = array( "Fahrzeug" => $fahrzeug, "Wert" => $wert, "Aktion" => $action, "Benutzer" => $benutzer, "Datum" => $datum );
            }
        }

        return $records;
    }

    public static function build($name) {
        $self = new self();

        switch( $name ) {
            case 'fahrer':
                $transactions = $self->fahrer();
                break;
            case 'user':
            default:
                $transactions[] = array("ErrorMessage" => "no reportdefinition selected, use '&definition=myreport' to get the report of definiton 'myreport'.");
                break;
        }

        return $transactions;
    }
}
?>