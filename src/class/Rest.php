<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

include_once('../config/settings.php');

require_once __DIR__.'/SimpleXLSX.php';

class Rest{
    
    private $config;

    private function transformFeature($row, $fields) {
        $title = '';
        $content = '';
        foreach ($fields as $field) {
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


        $properties = new stdClass();
        $properties->title = $title;
        $properties->description = $content;
        if (is_numeric($row['Marker'])) {
            $properties->{'style-class'} = $row['Marker'];
        } else {
            $properties->image = new stdClass();
            $properties->image->opacity = 1;
            $properties->image->src = 'data:image/svg+xml;utf8,' . file_get_contents('../resources/' . $row['Marker'] . '.svg');
        }

        $geometry = new stdClass();
        $geometry->type = 'Point';
        $geometry->coordinates = array(
            $row['KoordinateY'],
            $row['KoordinateX']
        );
        
        $feature = new stdClass();
        $feature->type = 'Feature';
        $feature->geometry = $geometry;
        $feature->properties = $properties;

        return $feature;
    }
    private function transformStyle($row) {
        $stroke = new stdClass();
        $stroke->color = $row['Linienfarbe'];
        $stroke->width = $row['Linienbreite'];

        $fill = new stdClass();
        $fill->color = $row['Farbe'];

        $vectorOptions = new stdClass();
        if ($row['Datei'] === '') {
            $vectorOptions->type = 'circle';
        } else {
            $icon = new stdClass();
            $icon->src = $row['Datei'];

            $vectorOptions->type = 'image';
            $vectorOptions->image = $icon;
        }
        $vectorOptions->radius = $row['Radius'];
        $vectorOptions->fill = $fill;
        $vectorOptions->stroke = $stroke;
        
        $style = new stdClass();
        $style->geomType = 'point';
        $style->value = $row['Marker'];
        $style->vectorOptions = $vectorOptions;

        return $style;
    }
    private function buildObject($obj) {
        $geojson = new stdClass();
        if ($obj === 'style') {
            $geojson->type = 'unique';
            $geojson->property = 'style-class';
            $geojson->values = [];
        }
        if ($obj === 'feature') {
            $geojson->type = 'FeatureCollection';
            $geojson->features = [];
        }
        if ($obj === 'system') {
            $geojson->type = 'SystemProperties';
            $geojson->properties = [];
		}
        return $geojson;
    }
    public function fileList(){
        $files = array();
        $this->config = new stdClass();

        foreach (new DirectoryIterator(DATA_PATH) as $file) {
            if ($file->isDot()) continue;
            if ($file->getExtension() == 'xlsx') {
                $files[] = $file->getBasename('.xlsx');
            }
        }
    
        sort($files);
    
        foreach ($files as $file) {

        }
        $this->config->files = $files;
    }
	public function getObjects($type, $obj, $obj_id) {
        $data = '';
        $this->fileList();

        if ($xlsx = SimpleXLSX::parse(DATA_PATH . $obj . '.xlsx')) {
            // Produce array keys from the array values of 1st array element
            $fields = $rows = [];

            $geojson = $this->buildObject($type);

            foreach ( $xlsx->rows($obj_id) as $k => $r ) {
                if ( $k === 0 ) {
                    $fields = $r;
                    continue;
                }
                if ($type === 'style') {
                    $geojson->values[] = $this->transformStyle( array_combine( $fields, $r) );
                }
                if ($type === 'feature') {
                    $geojson->features[] = $this->transformFeature( array_combine( $fields, $r), $fields );
                }
                if ($type === 'system') {
                    $geojson->properties[] = array_combine( $fields, $r );
                }
            }
            $data = $geojson;
        } else {
            $data = SimpleXLSX::parseError();
        }
		header('Content-Type: application/json');
		echo json_encode($data);	
    }
}
?>