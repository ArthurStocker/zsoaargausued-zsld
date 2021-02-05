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
			require_once 'class/Reports.php';
			if ( isset($_GET['definition']) ) {
				/**
				 * call build method of ReportingClass 
				 * 
				 * shomething like:
				 */
				$records = Reports::build( $_GET['definition'] );
			} else {
				/**
				 * but for now just deliver this report
				 * 
				 * begin_default_report
				 */
				$records = Reports::build( "" );
				/**
				 * end_default_report
				 */
			}
			if ( isset($records) ) {
				if ( isset($_GET['format']) ) {
					$rows[] = array_keys($records[0]);
					foreach ($records as $row) {
						$rows[] = $row;
					}
					switch( (string)$_GET['format'] ) {
						case 'xlsx':
							require_once 'class/SimpleXLSXGen.php';
							$xlsx = SimpleXLSXGen::fromArray( $rows );
							$xlsx->downloadAs('report.xlsx');
							break;
						case 'csv':
						default:
							require_once 'class/SimpleCSVGen.php';
							$csv = SimpleCSVGen::fromArray( $rows );
							$csv->downloadAs('report.csv');
							break;
					}
				} else {
					$response = new stdClass();
					$response->type = "Report";
					$response->objects = $records;
					header('Content-Type: application/json');
					echo json_encode($response, JSON_PRETTY_PRINT);
				}
			} else {
				header("HTTP/1.0 404 Not Found");
			}
		} else {
			header("HTTP/1.0 403 Forbidden");
		}
		break;
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		break;
}
?>