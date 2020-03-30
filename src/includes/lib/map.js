/**
 * ZSO Lagedarstellung API
 *
 * @todo
 */
// Define ZSO Lagedarstellung API
var zsld = {};


/**
 * GeoAdmin Map 
 *
 * 
 * 
 * TODO: 
 */
// Create a GeoAdmin Map
zsld.MAP = new ga.Map({

  // Define the div where the map is placed
  target: 'map',

  // Create a view
  view: new ol.View({

    // Define the default resolution
    // 10 means that one pixel is 10m width and height
    // List of resolution of the WMTS layers:
    // 650, 500, 250, 100, 50, 20, 10, 5, 2.5, 2, 1, 0.5, 0.25, 0.1
    resolution: 20,

    // Define a coordinate CH1903 (EPSG:2056) for the center of the view
    //center: [2660000, 1190000]
    center: [2651507.75, 1242189.5]
  })
});

/**
 * OpenLayer Parser und Helper
 *
 * 
 * 
 * TODO: 
 */
// GeoJSON Parser initialization
zsld.GEOJSONPARSER = new ol.format.GeoJSON();
