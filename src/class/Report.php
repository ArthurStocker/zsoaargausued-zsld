<?php
require_once 'class/ObjectStore.php';
require_once 'class/TransactionStore.php';

class Report {
    
    private $debug;
    private $format;
    private $records;
    private $filename;
    private $transactions;

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

    public function download() {
        if ( $this->format != "json" ) {
            $rows[] = array_keys($this->records[0]);
            foreach ($this->records as $row) {
                $rows[] = $row;
            }
            switch( $this->format ) {
                case 'xlsx':
                    require_once 'class/SimpleXLSXGen.php';
                    $xlsx = SimpleXLSXGen::fromArray( $rows );
                    $xlsx->downloadAs($this->filename . "." . $this->format);
                    break;
                case 'csv':
                default:
                    require_once 'class/SimpleCSVGen.php';
                    $csv = SimpleCSVGen::fromArray( $rows );
                    $csv->downloadAs($this->filename . '.csv');
                    break;
            }
            $this->transactions = "download";
        } else {
            $this->$transactions = new stdClass();
            $this->$transactions->type = "Report";
            $this->$transactions->objects = $records;
        }

        return $this->transactions;
    }

    public function build( $get ) {
        if ( isset($get['definition']) ) {
            $name = $get['definition'];
            $this->filename = $name;
        } else {
            $name = "";
            $this->filename = "error";
        }
        if ( isset($get['format']) ) {
            $this->format = $get['format'];
        } else {
            $this->format = "json";
        }
        switch( $name ) {
            case 'fahrer':
                $this->records = $this->fahrer();
                break;
            default:
                $this->records[] = array("ErrorMessage" => "no reportdefinition selected, use '&definition=myreport' to get the report of definiton 'myreport'.");
                break;
        }
    }
}
?>