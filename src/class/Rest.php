<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require_once __DIR__.'/Style.php';
require_once __DIR__.'/Feature.php';
require_once __DIR__.'/TextSearch.php';
require_once __DIR__.'/SystemProperty.php';
require_once __DIR__.'/ObjectStore.php';
require_once __DIR__.'/TransactionStore.php';
require_once __DIR__.'/SimpleXLSX.php';

include_once('../config/settings.php');

class Rest{
    
    private $debug;

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

        $response->stores = array_values(array_filter($filtered, function($v) {return $v->show;}));

        return $response;
    }
    public function create($data, $type, $obj, $id, $concurrent = false) {
        $response = null;

        if ($type === 'device') {
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
                    $response = ObjectStore::build( $objects->list($id) );
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
                        $response = SystemProperty::build( $xlsx->rows($id) );
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
        $response = TransactionStore::roll(DATA_PATH . $obj . '.json', $obj . '.json', $obj);
        if ( $response ) {
            return $response;
        }
    }
}
?>