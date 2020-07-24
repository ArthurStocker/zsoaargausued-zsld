<?php

class Reports {
    
    private $debug;

    private function fahrer() {
        $api = new Rest();
        
        if ( isset($_GET['transaction']) && isset($_GET['objectstore']) ) {
            $transactionstore = $api->read($_GET, 'transaction', (string)$_GET['transaction'], -1);
            foreach ($transactionstore->transactions as $transaction) {
                $ids = Array();
                $ids['oid'] = $transaction['uniquedevice'];
                $properties = $api->read($ids, 'objectstore', (string)$_GET['objectstore'], 0)->objects[0]['properties'];
                foreach (array_keys($properties) as $key) {
                    $transaction[$key] = $properties[$key];
                }
                $transactions[] = $transaction;
            }
        }

        return $transactions;
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