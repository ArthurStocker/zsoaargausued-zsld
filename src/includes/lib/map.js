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

zsld.MAP.addOverlay(
  (function() {
    // Popup showing and removing at the position the user clicked
    var element = document.createElement('div');
    element.setAttribute('id', 'popup');
    element.classList.add('popup');
    zsld.POPUP = new ol.Overlay({
      element: element
    });
    return zsld.POPUP;
  })()
);

zsld.MAP.on('singleclick', function(evt) {
  var feature = zsld.MAP.forEachFeatureAtPixel(evt.pixel, function(feat, layer) {
    return feat;
  });
  var element = $(zsld.POPUP.getElement());
  element.popover('destroy');
  if (feature) {
    zsld.POPUP.setPosition(evt.coordinate);
    console.debug("[{map.js} zsld.MAP.on('singleclick')] feature ", feature);
    element.popover({
      'placement': 'top',
      'animation': false,
      'html': true,
      'title': feature.get('title'),
      'content': feature.get('description')
    }).popover('show');
  }

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

/**
 * Bloodhound suggestion engine
 *
 * 
 * 
 * TODO: 
 */
// Bloodhound suggestion engine initialization 
zsld.BLOODHOUND = new Bloodhound({
  limit: 30,
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: GeoAdmin.serviceUrl + URL_GA_SEARCH,
    filter: function(locations) {
      return locations.results;
    }
  }
});
// This kicks off the loading and processing of local and prefetch data,
// the suggestion engine will be useless until it is initialized
zsld.BLOODHOUND.initialize();
