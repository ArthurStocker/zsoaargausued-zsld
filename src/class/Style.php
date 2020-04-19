<?php

class Style {
    
    private $debug;

    private function fill($row) {
        $object = new stdClass();
        $object->color =  $row['Farbe'];

        return $object;
    }
    private function stroke($row) {
        $object = new stdClass();
        $object->color = $row['Linienfarbe'];
        $object->width = $row['Linienbreite'];

        return $object;
    }
    private function options($row) {
        $shape = 'Datei'; // TODO: implement field "Form", $row['Form']
        if ($shape === 'Datei' && $row['Datei'] === '') {
            $shape = 'Kreis'; // default circle
        }

        $object = new stdClass();
        switch($shape) {
            case 'Datei':
                $object->type = 'icon';
                $object->scale = $row['Radius']/100;
                $object->src = $row['Datei'];
                break;
            default:
                $object->type = 'circle';
                $object->radius = $row['Radius'];
                break;
        }
        $object->fill = $this->fill($row);;
        $object->stroke = $this->stroke($row);

        return $object;
    }
    private function geometry($row) {
        $geometry = ''; // TODO: implement field "Geomerie", $row['Geomerie'], allways point for now

        $object = new stdClass();
        switch($geometry) {
            case 'x':
                $object->geomType = 'x';
                $object->value = $row['Marker'];
                $object->vectorOptions = $this->options($row);
                break;
            default:
                $object->geomType = 'point';
                $object->value = $row['Marker'];
                $object->vectorOptions = $this->options($row);
                break;
        }

        return $object;
    }
    private function response() {
        $response = new stdClass();
        $response->type = 'unique';
        $response->property = 'style-class';
        $response->values = [];

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
                $geojson->values[] = $self->geometry( array_combine( $headers, $r) );
            }
        }

        return $geojson;
    }
}
?>