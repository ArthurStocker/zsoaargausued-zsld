<?php

class Feature {
    
    private $debug;

    private function properties($row, $headers) {
        $title = '';
        $content = '';

        foreach ($headers as $field) {
            if (mb_substr($field, 0, 1, 'utf-8') === '§') {
                if ($title != '') {
                    $title = $title . '</br>' .  $row[$field];
                } else {
                    $title = $title . $row[$field];
                }
            }
            if (mb_substr($field, 0, 1, 'utf-8') === '+') {
                if (mb_substr($field, 1, 1, 'utf-8') === '{') {
                    $value = mb_substr($field, 2, null, 'utf-8') . ": " . $row[$field];
                } else {
                    $value = $row[$field];
                }
                if ($content != '') {
                    $content = $content . '</br>' .  $value;
                } else {
                    $content = $content . $value;
                }                
            }
        }

        $object = new stdClass();
        $object->title = $title;
        $object->description = $content;
        $object->{'style-class'} = $row['Marker'];

        return $object;
    }
    private function geometry($row) {
        $geometry = ''; // TODO: implement field "Geomerie", $row['Geomerie'], allways Point for now

        $object = new stdClass();
        switch($geometry) {
            case 'x':
                $object->type = 'x';
                $object->coordinates = array(
                    $row['KoordinateY'],
                    $row['KoordinateX']
                );
                break;
            default:
                $object->type = 'Point';
                $object->coordinates = array(
                    $row['KoordinateY'],
                    $row['KoordinateX']
                );
                break;
        }

        return $object;
    }
    private function features($row, $headers) {
        $object = new stdClass();
        $object->type = 'Feature';
        $object->geometry = $this->geometry($row);
        $object->properties = $this->properties($row, $headers);

        return $object;
    }
    private function response() {
        $response = new stdClass();
        $response->type = 'FeatureCollection';
        $response->features = [];

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
                $geojson->features[] = $self->features( array_combine( $headers, $r), $headers );
            }
        }

        return $geojson;
    }
}
?>