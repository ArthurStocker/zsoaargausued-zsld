<?php

class SystemProperty {
    
    private $debug;

    private function response() {
        $response = new stdClass();
        $response->type = 'SystemProperties';
        $response->properties = [];

        return $response;
    }
    public static function build($rows) {
        $self = new self();
        $geojson = $self->response();
        $headers = []; // Produce array keys from the array values of 1st array element
        
        if ($rows) {
            foreach ( $rows as $k => $r ) {
                if ( $k === 0 ) {
                    $headers = $r;
                    continue;
                }
                $geojson->properties[] = array_combine( $headers, $r);
            }
        }

        return $geojson;
    }
}
?>