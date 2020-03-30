/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * CODEPEN Config
 */
// Voreingestellte Endpunkte 
var URL_GEOJSON_STYLE = '//zso-aargausued.ch/map/api/read.php?type=style&object=default&id=0';
var URL_GEOJSON_FEATURE = '//zso-aargausued.ch/map/api/read.php?type=feature&object=lage&id=0';
var URL_GEOJSON_FEATURE_YAH = '//zso-aargausued.ch/map/api/read.php?type=feature&object=standort&id=0';

// HTML mit den Endpunkten beladen
setUrl('style-url', URL_GEOJSON_STYLE);
setUrl('feature-url', URL_GEOJSON_FEATURE);
setUrl('feature-yha-url', URL_GEOJSON_FEATURE_YAH);

var FADING_DURATION = 500;
var DISPLAY_DURATION = 5000;
