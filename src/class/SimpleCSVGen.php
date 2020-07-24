<?php
/**
 * Convert a multi-dimensional, associative array to CSV data
 * @param  array $data the array of data
 * @return string       CSV text
 */

class SimpleCSVGen {

	public $rows;
	public $template;
	public function __construct() {
		$this->rows = [];
		$this->template = [];
	}
	public static function fromArray( array $rows ) {
		$xlsx = new self();
		$xlsx->setRows( $rows );
		return $xlsx;
	}
	public function setRows( $rows ) {
		if ( is_array( $rows ) && isset( $rows[0] ) && is_array($rows[0]) ) {
			$this->rows = $rows;
		} else {
			$this->rows = [];
		}
	}
	public function __toString() {
		$fh = fopen( 'php://memory', 'wb' );
		if ( ! $fh ) {
			return '';
		}

		if ( ! $this->_generate( $fh ) ) {
			fclose( $fh );
			return '';
		}
		$size = ftell( $fh );
		fseek( $fh, 0);

		return (string) fread( $fh, $size );
	}
	public function saveAs( $filename ) {
		$fh = fopen( $filename, 'wb' );
		if (!$fh) {
			return false;
		}
		if ( !$this->_generate($fh) ) {
			fclose($fh);
			return false;
		}
		fclose($fh);

		return true;
	}
	public function download() {
		return $this->downloadAs( gmdate('YmdHi') . '.xlsx' );
	}
	public function downloadAs( $filename ) {
		$fh = fopen('php://memory','wb');
		if (!$fh) {
			return false;
		}

		if ( !$this->_generate( $fh )) {
			fclose( $fh );
			return false;
		}

		$size = ftell($fh);

		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T' , time() ));
		header('Content-Length: '.$size);

		while( ob_get_level() ) {
			ob_end_clean();
		}
		fseek($fh,0);
		fpassthru( $fh );

		fclose($fh);
		return true;
	}

	private function _generate( $fh ) {

		
		if (!$fh) {
			return false;
		}

		if ( count($this->rows)  ) {
			# write out the data
			foreach ( $this->rows as $row ) {
				fputcsv($fh, $row);
			}
		}

		return true;
	}
}
