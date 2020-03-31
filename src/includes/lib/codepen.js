/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * CODEPEN Config
 */
// Voreingestellte Endpunkte 
var URL_GEOJSON_STYLE = '//zso-aargausued.ch/map/api/read.php?type=style&object=default&id=0';
var URL_GEOJSON_FEATURE = '//zso-aargausued.ch/map/api/read.php?type=feature&object=lage&id=0';
var URL_GEOJSON_FEATURE_YAH = '//zso-aargausued.ch/map/api/read.php?type=feature&object=standort&id=0';

var URL_GA_SEARCH = '/rest/services/api/SearchServer?sr=2056&searchText=%QUERY&type=locations';

// HTML mit den Endpunkten beladen
setUrl('style-url', URL_GEOJSON_STYLE);
setUrl('feature-url', URL_GEOJSON_FEATURE);
setUrl('feature-yha-url', URL_GEOJSON_FEATURE_YAH);

// ZSO Lagedarstellung Einstellungen
var URL_ZSLD_SETTINGS = '//kp.zso-aargausued.ch:8443/map/api/read.php?type=system&object=settings&id=4';

var FADING_DURATION = 500;
var DISPLAY_DURATION = 5000;
