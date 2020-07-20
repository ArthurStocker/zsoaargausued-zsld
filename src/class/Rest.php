<?php
require_once 'class/Style.php';
require_once 'class/Feature.php';
require_once 'class/TextSearch.php';
require_once 'class/SystemProperty.php';
require_once 'class/ObjectStore.php';
require_once 'class/TransactionStore.php';
require_once 'class/SimpleXLSX.php';


class Rest {
    
    private $debug;

    function __construct($debug = false) {
        $this->debug = $debug;
    }

    public function list($filter) {
        $filtered = [];
        $response = new stdClass();
        $response->type = 'Stores';

        foreach (new DirectoryIterator(DATA_PATH) as $file) {
            if ($file->isDot()) continue;
            $object = new stdClass();
            $object->show = ($filter === '' || $file->getExtension() === $filter);
            $object->type = $file->getExtension();
            $object->name = $file->getBasename('.' . $file->getExtension());
            $object->server = $_SERVER['HTTP_HOST'];
            $object->fullname = $file->getBasename();

            $filtered[] = $object;
        }
    
        usort($filtered, function($a, $b) {return strcmp($a->fullname, $b->fullname);});

        if (DeviceTAC::isValid()) {
            $response->stores = array_values(array_filter($filtered, function($v) {return $v->show;}));
        } else {
            $response->stores = [];
        }

        return $response;
    }
    public function create($data, $type, $obj, $id, $concurrent = false) {
        $response = null;

        if ($type === 'access' || $type === 'device' || $type === 'iam') {
            $response = ObjectStore::save($data, $type, $id, $concurrent, DATA_PATH . $obj . '.json');
        } else {
            $response = TransactionStore::save($data, $type, $id, $concurrent, DATA_PATH . $obj . '.json');
        }
        return $response;
    }
	public function read($data, $type, $obj, $id) {
        $response = '';
        if ($type === 'list') {
            $response = $this->list($obj);
        } elseif ($type === 'objectstore') {
            if (isset($obj)) {
                if ($objects = ObjectStore::parse(DATA_PATH . $obj . '.json')) {
                    $response = ObjectStore::build( $objects->list($id, $data) );
                } else {
                    $response = ObjectStore::parseError();
                }
            } else {
                $response = "Error: 'Missing object!'";
            }
        } elseif ($type === 'transaction') {
            if (isset($obj)) {
                if ($store = TransactionStore::parse(DATA_PATH . $obj . '.json')) {
                    $response = TransactionStore::build( $store->list($id) );
                } else {
                    $response = TransactionStore::parseError();
                }
            } else {
                $response = "Error: 'Missing object!'";
            }
        } else {
            if (isset($obj)) {
                if ($xlsx = SimpleXLSX::parse(DATA_PATH . $obj . '.xlsx')) {
                    if ($type === 'style') {
                        $response = Style::build( $xlsx->rows($id) );
                    }
                    if ($type === 'feature') {
                        $response = Feature::build( $xlsx->rows($id) );
                    }
                    if ($type === 'search') {
                        $response = TextSearch::build( $xlsx->rows($id), $data );
                    }
                    if ($type === 'system') {
                        $response = SystemProperty::build( $xlsx->rows($id), ($id == 2) );
                    }
                } else {
                    $response = SimpleXLSX::parseError();
                }
            } else {
                $response = "Error: 'Missing object!'";
            }
        }
		header('Content-Type: application/json');
		echo json_encode($response, JSON_PRETTY_PRINT);	
    }
    public function update($data, $type, $obj, $id) {
        if ($type === 'access' || $type === 'device' || $type === 'iam') {
            $response = ObjectStore::update($data, $type, $id, DATA_PATH . $obj . '.json');
        } elseif ($type === 'objectstore') {
            $response = ObjectStore::roll(DATA_PATH . $obj . '.json', $obj . '.json', $obj);
        } elseif ($type === 'transaction') {
            $response = TransactionStore::roll(DATA_PATH . $obj . '.json', $obj . '.json', $obj);
        }
        if ( $response ) {
            return $response;
        }
    }
}
?>