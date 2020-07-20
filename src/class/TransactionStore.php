<?php

class TransactionStore {
    
    private $debug;
    private $errno = 0;
    private $error = false;

    private $records = [];
    private $transaction = null;

    private function _createTIC() { 
        // Create a token
        $token      = $_SERVER['HTTP_HOST'];
        $token     .= $_SERVER['REQUEST_URI'];
        $token     .= uniqid(rand(), true);
        
        // ID is 128-bit hex
        $hash       = strtoupper(md5($token));
        
        // Create ID
        $htic       = '';
        $htic      .= substr($hash,  0,  8) . 
                      substr($hash,  8,  4) .
                      substr($hash, 12,  4) .
                      substr($hash, 16,  4) .
                      substr($hash, 20, 12);
                
        return $htic;
    }
    private function _add($data, $type, $id, $concurrent = false) {
        $state = '';

        if ( !$concurrent) {
            foreach ($this->records as $row) {
                if ($row['id'] === $id && $row['type'] === $type) {
                    $state = ( $row['data'] !== $data );
                }
            }
        }
        
        $object = new stdClass();
        $object->id = $id;
        $object->tic = $this->_createTIC();
        $object->type = $type;
        $object->data = $data;
        $object->valid = date(DATE_ATOM);
        $object->decision = date(DATE_ATOM);
        $object->transaction = date(DATE_ATOM);

        if (defined("DEVICE_TAC")) {
            $object->uniquedevice = constant("DEVICE_TAC");
        } else {
            $object->uniquedevice = '';
        }

        $this->transaction = (array)$object;

        if ($concurrent || $state) {
            $this->records[] = (array)$object;
        } else {
            $this->error(2, 'Transaction failed due to concurrency violation!');
        }
    }
    private function _get($id) {
        $filtered = [];
        $transactions = [];

        if ((int)$id > -1) {
            foreach ($this->records as $rec) {
                $transactions[$rec['id']][] = $rec;
            }

            foreach ($transactions as $object) {
                
                usort($object, function($a, $b) {return strcmp($b['decision'], $a['decision']);});

                foreach ($object as $row => $rec) {
                    if ((int)$row === (int)$id) {
                        $filtered[] = $rec;
                    }
                }
            }
        } else {
            $filtered = $this->records;
        }
        return $filtered;
    }
    private function _parse($filename) {
        if ($json = file_get_contents($filename)) {
            //$this->records = usort(json_decode($json, true), function($a, $b) {return strcmp($a['decision'], $b['decision']);});
            $this->records = json_decode($json, true);
        } else {
            $this->error(1, 'Cannot read file [' . $filename . ']!');
        }

    }
    private function response() {
        $response = new stdClass();
        $response->type = 'TransactionStore';
        $response->transactions = [];

        return $response;
    }
    public function list($id) {
        $filtered = $this->_get($id);
        return $filtered;
    }
    public function success() {
		return ! $this->error;
    }
    public function error( $num = null, $str = null ) {
		if ( $num ) {
			$this->errno = $num;
			$this->error = $str;
			if ( $this->debug ) {
				trigger_error( __CLASS__ . ': ' . $this->error, E_USER_WARNING );
			}
		}

		return $this->error;
	}
	public function errno() {
		return $this->errno;
    }
    public static function build($rows) {
        $store = new self();
        $geojson = $store->response();
        
        if (DeviceTAC::isValid() && $rows) {
            $geojson->transactions = $rows;
        }

        return $geojson;
    }
    public static function parseError( $set = false ) {
		static $error = false;
		return $set ? $error = $set : $error;
	}
	public static function parseErrno( $set = false ) {
		static $errno = false;
		return $set ? $errno = $set : $errno;
	}
    public static function parse($filename, $debug = false) {
		$store = new self();
		$store->debug = $debug;
		if ( file_exists($filename) ) {
			$store->_parse($filename);
		}
		if ( $store->success() ) {
			return $store;
		}
		self::parseError( $store->error() );
		self::parseErrno( $store->errno() );

		return false;
    }
    public static function save($data, $type, $id, $concurrent = false, $filename, $debug = false) {
		$store = new self();
        $store->debug = $debug;
        if ( DeviceTAC::isValid() ) {
            if ( file_exists($filename) ) {
                $store->_parse($filename);
            }
            if ( $store->success() ) {
                $store->_add($data, $type, $id, $concurrent); 
            }
            if ($store->success() && !file_put_contents($filename, json_encode($store->records, JSON_PRETTY_PRINT))) {
                $store->error(3, 'Transaction failed! Error writing transaction, try again or call MISSION CONTROL CENTER.');
            }
        } else {
            $store->error(403, 'Transaction failed! Access prohibited.');
        }
        $store->transaction['errno'] = $store->errno;
        $store->transaction['error'] = $store->error;
        return $store->transaction;
    }
    public static function roll($filename, $basename, $obj, $debug = false) {
		$store = new self();
        $store->debug = $debug;
        if ( DeviceTAC::isValid() ) {
            if ( file_exists($filename) ) {
                $store->_parse($filename);
            }
            if ( $store->success() ) {
                if ( extension_loaded('zip') ) {
                    $zip = new ZipArchive();
                    $archive = $obj . '-' . time() . '.zip';
                    if ($zip->open(DATA_PATH . $archive, ZIPARCHIVE::CREATE) !== true) {
                        $store->error(4, 'Transaction failed! Failed to create ziparchive.');
                    } else {
                        $zip->addFile($filename, $basename);
                        $zip->close();
                        unlink($filename);
                    }
                } else {
                    $store->error(5, 'Transaction failed! ZIP extension not available.');
                }
            }
            if ($store->success() && !file_put_contents($filename, json_encode($store->list(0), JSON_PRETTY_PRINT))) {
                $store->error(6, 'Transaction failed! Error in rollover transactions, check if rollback is needed.');
            }
            if ( $store->success() ) {
                if(file_exists(DATA_PATH . $archive)) {  
                    // push to download the zip  
                    header('Content-type: application/zip');  
                    header('Content-Disposition: attachment; filename="' . $archive . '"');  
                    readfile(DATA_PATH . $archive);  
                    // remove ziparchive 
                    unlink(DATA_PATH . $archive);  
                }
                //return $store->list(0);
            } else {
                return $store->records;
            }
        } else {
            $store->error(403, 'Transaction failed! Access prohibited.');
        }
        $store->transaction['errno'] = $store->errno;
        $store->transaction['error'] = $store->error;
        return $store->transaction;
    }
}
?>