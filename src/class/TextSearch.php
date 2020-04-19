<?php

class TextSearch {
    
    private $debug;

    private function response() {
        $response = new stdClass();
        $response->type = 'TextSearch';
        $response->options = [];
        $response->results = [];

        return $response;
    }
    public static function build($rows, $data) {
        $self = new self();
        $geojson = $self->response();
        $headers = []; // Produce array keys from the array values of 1st array element
        $selected = [];

        $geojson->options['keys'] = json_decode($data['keys']);
        $geojson->options['value'] = $data['value'];
        $geojson->options['format'] = json_decode($data['format']);
        $geojson->options['delimiter'] = $data['delimiter'];

        if ($rows) {
            foreach ( $rows as $k => $r ) {
                if ( $k === 0 ) {
                    $headers = $r;
                    continue;
                }
                $record = array_combine( $headers, $r);
                foreach ( $geojson->options['keys'] as $f ) {
                    $string = '';
                    if ( property_exists( $geojson->options['format'], $f ) ) {
                        if ( property_exists( $geojson->options['format']->$f, "p" ) ) {
                            $string .= $geojson->options['format']->$f->p;
                        }
                        $string .= $record[$f];
                        if ( property_exists( $geojson->options['format']->$f, "s" ) ) {
                            $string .= $geojson->options['format']->$f->s;
                        }
                    } else {
                        $string = $record[$f];
                    }
                    $selected[] = $string;
                }
                $string = join( $geojson->options['delimiter'], $selected );
                if ( strpos( $string , $geojson->options['value'] ) ) {
                    $object = new stdClass();
                    $object->name = join( $geojson->options['delimiter'], $selected );
                    $object->properties = $record;

                    if (defined("DEVICE_TAC")) {
                        $object->executingdevice = constant("DEVICE_TAC");
                    } else {
                        $object->executingdevice = '';
                    }

                    $geojson->results[] = $object;
                }
                $selected = [];
            }
        }

        return $geojson;
    }
}
?>