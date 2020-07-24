<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, OPTIONS");


require_once 'class/Rest.php';

$api = new Rest();


$requestMethod = $_SERVER["REQUEST_METHOD"];

switch($requestMethod) {
	case 'GET':
		if ( DeviceTAC::read( 'auth' ) ) {
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
			if ( isset($transactions) ) {
				if ( isset($_GET['format']) ) {
					switch( (string)$_GET['format'] ) {
						case 'csv':
						default:
							header('Content-Type: application/csv');
							header('Content-Disposition: attachment; filename=report.csv');
							header('Pragma: no-cache');
							echo getcsv($transactions);
							break;
					}
				} else {
					$response = new stdClass();
					$response->type = "Report";
					$response->objects = $transactions;
					header('Content-Type: application/json');
					echo json_encode($response, JSON_PRETTY_PRINT);
				}
			} else {
				header("HTTP/1.0 404 Not Found");
			}
		} else {
			header("HTTP/1.1 403 Forbidden");
		}
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}

/**
 * Convert a multi-dimensional, associative array to CSV data
 * @param  array $data the array of data
 * @return string       CSV text
 */
function getcsv($data) {
	# Generate CSV data from array
	$fh = fopen('php://temp', 'rw'); # don't create a file, attempt
									 # to use memory instead

	# write out the headers
	fputcsv($fh, array_keys(current($data)));

	# write out the data
	foreach ( $data as $row ) {
			fputcsv($fh, $row);
	}
	rewind($fh);
	$csv = stream_get_contents($fh);
	fclose($fh);

	return $csv;
}
?>