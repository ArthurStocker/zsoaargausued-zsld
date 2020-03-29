/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * Die konfiguration ist in der Produktiven umgebung im PHP
 * config/settings.php
 * TODO: konfig aus dem einstellungen.xlsx lesen
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


/**
 * Allgemeine Funktionen und Helper
 *
 * 
 * 
 * TODO: 
 */
// Endpunkte setzen
function setUrl(name, value) {
  var elRequest = ':input[name=' + name + ']';
  $(elRequest).val(value);
}

// Endpunkte lesen
function getUrl(name) {
  var elRequest = ':input[name=' + name + ']';
  return $(elRequest).val();
}

// Show info message
function info(msg) {
  $('.alert-info').text(msg);
  $('.alert-info').toggle(FADING_DURATION).delay(DISPLAY_DURATION).toggle(FADING_DURATION);
}

// Show danger message
function failed(msg) {
  $('.alert-danger').text(msg);
  $('.alert-danger').toggle(FADING_DURATION).delay(DISPLAY_DURATION).toggle(FADING_DURATION);
}

// Show success message
function passed(msg) {
  $('.alert-success').text(msg);
  $('.alert-success').toggle(FADING_DURATION).delay(DISPLAY_DURATION).toggle(FADING_DURATION);
}


/**
 * GeoAdmin Map global erstellen und ZSO Lagedarstellung API definieren
 *
 * 
 * 
 * TODO: 
 */
// Create ZSO Lagedarstellung API
var zsld = {};

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
 * GeoAdmin Map Layer global erstellen
 *
 * 
 * 
 * TODO: Initiale visibilität aus den Einstellungen lesen und anwenden
 */
// Create a background layer
zsld.BackgroundLayer = ga.layer.create('ch.swisstopo.pixelkarte-farbe');

// Create a coordinate grid CH1903+/LV95 (org.epsg.grid_2056)
zsld.KoordinatennetzLayer = ga.layer.create('org.epsg.grid_2056');

// Create a community grid layer
zsld.GemeindeLayer = ga.layer.create('ch.swisstopo.swissboundaries3d-gemeinde-flaeche.fill');

// Vector layer variable declaration and initialization
zsld.olSource = new ol.source.Vector();
zsld.olVector = new ol.layer.Vector({
  source: zsld.olSource
});

// Add the layers to the map
zsld.MAP.addLayer(zsld.BackgroundLayer);
zsld.MAP.addLayer(zsld.KoordinatennetzLayer);
zsld.MAP.addLayer(zsld.GemeindeLayer);


// set Initilal visibility to false
zsld.GemeindeLayer.setVisible(false);


/**
 * GeoAdmin Map Layer Helper
 *
 * 
 * 
 * TODO: 
 */
// GeoJSON Parser variable declaration and initialization
zsld.olGeoJSONParser = new ol.format.GeoJSON();

// Check if the map has a vector layer
// TODO: Is this really deterministic?
//       Or do we have to check details of each layer.
function hasVectorOnMap() {
  if (zsld.MAP.getLayers().getArray().length > 1) {
    return true;
  }
};



// Load and apply features file function
var setLayerSource = function() {
  $.ajax({
    type: 'GET',
    url: getUrl('feature-url'),
    success: function(data) {
      zsld.olSource.clear();
      zsld.olSource.addFeatures(
        zsld.olGeoJSONParser.readFeatures(data)
      );
      passed('');
    },
    error: function() {
      zsld.olSource.clear();
      failed('');
    }
  });
};

// Load and apply styling file function
var setLayerStyle = function() {
  $.ajax({
    type: 'GET',
    url: getUrl('style-url'),
    success: function(data) {
      var olStyleForVector = new ga.style.StylesFromLiterals(data);
      zsld.olVector.setStyle(function(feature) {
        return [olStyleForVector.getFeatureStyle(feature)];
      });
      $('#editor').text(JSON.stringify(data, null, 4));
      passed('');
    },
    error: function() {
      failed('');
    }
  });
};

// Load styling file in editor
var loadStyling = function() {
  $.ajax({
    type: 'GET',
    url: getUrl('style-url'),
    success: function(data) {
      //$('#editor').val('');
      $('#editor').text(JSON.stringify(data, null, 4));
      passed('');
    },
    error: function() {
      failed('');
    }
  });
};

// Apply style changes from editor
var applyStyling = function() {
  if (hasVectorOnMap()) {
    zsld.MAP.removeLayer(zsld.olVector);
  }
  var data = JSON.parse($('#editor').val());
  var olStyleForVector = new ga.style.StylesFromLiterals(data);
  zsld.olVector.setStyle(function(feature) {
    return [olStyleForVector.getFeatureStyle(feature)];
  });
  setLayerSource();
  zsld.MAP.addLayer(zsld.olVector);
};

// Apply GeoJSON config from urls
var addLayer = function() {

  // Load Styling file
  setLayerStyle();

  // Load Geojson file
  setLayerSource();

  // Only one vector layer is added
  if (hasVectorOnMap()) {
    zsld.MAP.removeLayer(zsld.olVector);
  }

  // Add Geojson layer
  zsld.MAP.addLayer(zsld.olVector);

}

// Remove vector layer from map
var removeLayer = function() {
  if (zsld.olVector) {
    zsld.MAP.removeLayer(zsld.olVector);
  }
};

// Popup showing and removing at the position the user clicked
var element = document.createElement('div');
element.setAttribute('id', 'popup');
//element.setAttribute('title', 'Details');
element.classList.add('popup');
var popup = new ol.Overlay({
  element: element
});
zsld.MAP.addOverlay(popup);

zsld.MAP.on('singleclick', function(evt) {
  var feature = zsld.MAP.forEachFeatureAtPixel(evt.pixel, function(feat, layer) {
    return feat;
  });
  var element = $(popup.getElement());
  element.popover('destroy');
  if (feature) {
    popup.setPosition(evt.coordinate);
    console.log('feature details: ', feature);
    element.popover({
      'placement': 'top',
      'animation': false,
      'html': true,
      'title': feature.get('title'),
      'content': feature.get('description')
    }).popover('show');
  }

});




// StyleFactory Class
function StyleFactory() {
  var styles = [];

  this.$get = function() {
    var selectStroke = new ol.style.Stroke({
      color: [255, 128, 0, 1],
      width: 3
    });

    var selectFill = new ol.style.Fill({
      color: [255, 255, 0, 0.75]
    });

    var selectStyle = new ol.style.Style({
      fill: selectFill,
      stroke: selectStroke,
      image: new ol.style.Circle({
        radius: 10,
        fill: selectFill,
        stroke: selectStroke
      })
    });

    var highlightStroke = new ol.style.Stroke({
      color: [255, 128, 0, 1],
      width: 6
    });

    var highlightFill = new ol.style.Fill({
      color: [255, 128, 0, 1]
    });

    var highlightStyle = new ol.style.Style({
      fill: highlightFill,
      stroke: highlightStroke,
      image: new ol.style.Circle({
        radius: 10,
        fill: highlightFill,
        stroke: highlightStroke
      })
    });

    function filter(name) {
      return styles.filter(function(element, index, array) {
        return element.hasOwnProperty(name);
      });
    }

    styles.push({ select: selectStyle });
    styles.push({ highlight: highlightStyle });

    var Controller = function() {
      this.add = function(name, olStyle) {
        if (filter(name).length == 0) {
          var item = {};
          item[name] = olStyle;
          styles.push(item);
          return true;
        }
        return false;
      };
      this.get = function(style) {
        return filter(style)[0][style];
      };
    };
    return new Controller();
  };
}

// FeatureFactory Class
function FeatureFactory() {
  this.$get = function(gaMap, olSource, olVector, olStyle, parser) {
    // Remove features associated with a layer.
    function removeFromLayer(olLayer) {
      var features = olSource.getFeatures();
      for (var i = 0, ii = features.length; i < ii; i++) {
        var layerId = features[i].get('layerId');
        if (layerId === olLayer.id) {
          olSource.removeFeature(features[i]);
        }
      }
    }

    // Add/remove/move to the top of the vector layer.
    function updateLayer(gaMap) {
      if (!olSource.getFeatures().length) {
        ol.Observable.unByKey(listenerKeyRemove);
        gaMap.removeLayer(olVector);
      } else if (gaMap.getLayers().getArray().indexOf(olVector) === -1) {
        gaMap.addLayer(olVector);

        // Add event for automatically removing the features when the
        // corresponding layer is removed.
        listenerKeyRemove = gaMap.getLayers().on('remove', function(event) {
          removeFromLayer(event.element);
          updateLayer(gaMap);
        });
      }
    }

    var Controller = function() {
      this.addmultiple = function(features) {

      };
      this.add = function(featureSet, type) {
        if (type == 'object') {
          var olFeature = parser.readFeature(featureSet);
          console.log(olFeature);
          olFeature.setStyle(olStyle);
          olSource.addFeature(olFeature);
        } else {
          var olFeatures;
          if (type == 'array') {
            olFeatures = [];
            for (f = 0; f < featureSet.length; f++) {
              olFeatures.push(parser.readFeature(featureSet[f]));
            }
          } else if (type == 'collection') {
            olFeatures = parser.readFeatures(featureSet);
          }
          console.log(olFeatures);
          for (f = 0; f < olFeatures.length; f++) {
            olFeatures[f].setStyle(olStyle);
          }
          olSource.addFeatures(olFeatures);
        }
        updateLayer(gaMap);
      };
      this.get = function() {

        return abc;
      };
    };
    return new Controller();
  };
}

//
var controller = (new FeatureFactory()).$get(zsld.MAP, zsld.olSource, zsld.olVector, ((new StyleFactory()).$get()).get('select'), zsld.olGeoJSONParser);

// ApiFactory Class
var ApiFactory = function() {
  var success = '';
  var error = '';
  var type = '';
  var url = '';

  function request() {
    $.ajax({
      type: type,
      url: url,
      success: passed,
      error: error
    });
  }

  this.$get = function(s, e) {
    if (s instanceof Function) {
      success = s;
    } else {
      success = function(data) {
        console.log('REST call response: ', data);
        passed('REST call passed');
      }
    }

    if (e instanceof Function) {
      error = e;
    } else {
      error = function() {
        console.log('REST call response: ', data);
        failed('REST call failed');
      }
    }

    var Endpoint = function() {
      this.get = function(u, p) {
        v = {};
        type = 'GET';
        hasParams = false;

        for (var i in p.params) {
          if (p.params.hasOwnProperty(i)) {
            hasParams = true;
            if (p.params[i])
              v[i] = p.params[i];
          }
        }
        url = hasParams ? u + '?' + $.param(v) : u;

        request();
      };
      /**
       * Return obj with objects properties
       * @param objects[1...n]
       * @returns obj a new object based on objects
       */
      this.extend = function() {
        var obj = {};
        for (_obj in arguments) {
          for (var i in arguments[_obj]) {
            if (arguments[_obj].hasOwnProperty(i)) {
              obj[i] = arguments[_obj][i];
            }
          }
        }
        return obj;
      }
    };
    return new Endpoint();
  };
}

// Query Class
function Query() {

  // Params:
  //   - imageDisplay
  //   - mapExtent
  var getMapParams = function(olMap, dpi) {
    var mapSize = olMap.getSize();
    return {
      imageDisplay: mapSize.concat([dpi]).toString(),
      mapExtent: olMap.getView().calculateExtent(mapSize).toString(),
      sr: olMap.getView().getProjection().getCode().split(':')[1]
    };
  };

  // Params:
  //   - layers
  //   - timeInstant
  var getLayersParams = function(olLayers, gaTime) {
    var bodIds = [];
    var timeInstant = [];
    olLayers.forEach(function(item) {
      if (item.id) {
        bodIds.push(item.id);
        var ti = '';
        if (item.timeEnabled) {
          ti = gaTime.get() || gaTime.getYearFromTimestamp(item.time);
          timeInstant.push(ti);
        }
      }
    });
    return {
      layers: 'all:' + bodIds.toString(),
      timeInstant: timeInstant.toString() || undefined
    };
  };

  // Params:
  //   - geometry
  //   - geometryType
  //   - geometryFormat
  var getGeometryParams = function(olGeometry) {
    var geometry;
    var geometryType;
    if (olGeometry instanceof ol.geom.Point) {
      geometry = olGeometry.getCoordinates().toString();
      geometryType = 'esriGeometryPoint';
    } else if (olGeometry) {
      geometry = olGeometry.getExtent().toString();
      geometryType = 'esriGeometryEnvelope';
    }
    // TODO: manage esriGeometryPolyline and esriGeometryPolygon
    return {
      geometry: geometry,
      geometryType: geometryType,
      geometryFormat: 'geojson'
    };
  };

  this.$get = function($http, gaGeoAdmin, gaTime) {
    var DPI = 96;
    var url = gaGeoAdmin.serviceUrl +
      '/rest/services/all/MapServer/identify';

    var Identify = function() {
      this.get = function(olMap, olLayers, olGeometry, tolerance,
        returnGeometry, timeout, limit, order, offset, where) {
        if (!olMap || !olLayers) {
          return console.log('Missing required parameters');
        }
        var mapParams = getMapParams(olMap, DPI);
        var layersParams = getLayersParams(olLayers, gaTime);
        var geometryParams = getGeometryParams(olGeometry);
        var othersParams = {
          tolerance: tolerance || 0,
          returnGeometry: !!returnGeometry,
          lang: gaGeoAdmin.lang
        };
        if (limit) {
          othersParams.limit = limit;
        }
        if (order) {
          othersParams.order = order;
        }
        if (offset) {
          othersParams.offset = offset;
        }
        if (where) {
          othersParams.where = where;
        }
        var params = $http.extend(mapParams, layersParams, geometryParams,
          othersParams);
        var timeo = timeout || {}; // could be an integer or a canceler
        return $http.get(url, {
          timeout: timeo.promise || timeo,
          params: params,
          cache: true
        });
      };
    };
    return new Identify();
  };
}


//
function test(data) {
  console.log(data);
  controller.add(data.results[0]);
}

//
function processor(data) {
  console.log(data.results);
  if (data.results.length == 1) {
    controller.add(data.results[0], 'object');
  } else if (data.results.length > 1) {
    controller.add(data.results, 'array');
  }
}

//
var query = (new Query()).$get((new ApiFactory()).$get(processor), GeoAdmin, null);




//... query

var getCondition = function(e) {
  return $(e).parents('[ga-index]');
}

var getConditions = function() {
  return $('[ga-query] [ga-index]');
}

var getLastCondition = function() {
  return $('[ga-query] [ga-index]:last');
}

var getConditionIndex = function(e) {
  return getCondition(e).attr('ga-index');
}

var getLastConditionIndex = function() {
  return getLastCondition().attr('ga-index');
}

var renumberConditionIndexs = function(conditions) {
  conditions.each(function(index) {
    var ga_index = index * 1 + 1;
    $(this).attr('ga-index', ga_index);
    $(this).find('[ga-header] label').text('Bedingung Nr. ' + ga_index);
  });
}

var copyCondition = function(e) {
  getCondition(e).clone().appendTo('[ga-query]');
  renumberConditionIndexs(getConditions());
}

var resetCondition = function(e) {

}

var removeCondition = function(e) {
  if (getLastConditionIndex() > 1) {
    getCondition(e).remove();
    renumberConditionIndexs(getConditions());
  } else {

  }
}

var removeTerms = function(condition, value) {
  return $(condition).parents('[ga-body]').find('[ga-term="' + value + '"]').parent().remove();
}

var appendTerms = function(condition, value) {
  return $('[ga-body-templates] [ga-term="' + value + '"]').parent().clone().appendTo($(condition).parents('[ga-body]'));
}

var selectChange = function(e) {
  var term = $(e).attr('ga-term');
  var value = $(e).prop('value');
  var index = getConditionIndex(e);
  var previous = $(e).attr('ga-selectedIndex');
  var selected = $(e).prop('selectedIndex');

  $(e).attr('ga-selectedIndex', selected);

  removeTerms(e, $(e).find('option:eq(' + previous + ')').prop('value'));
  appendTerms(e, value);

  //alert('Handler for .change() of condition ' + index + ' called. Selected option changed from ' + previous + ' to ' + selected + '. Term ' + term + ' has value ' + value + '.');
}

var execQuery = function() {
  // Show the community grid layers on the map
  zsld.GemeindeLayer.setVisible(true);

  var lyrs = new ol.Collection();
  lyrs.push(zsld.GemeindeLayer);

  var string = '';
  var conditions = getConditions();
  conditions.each(function(c_index) {
    var type = '';
    if (string.length > 0 && c_index * 1 < conditions.length) {
      string += ' or ';
    }
    $(this).find('[ga-term]').each(function(t_index) {
      if (t_index * 1 > 0) {
        string += ' ';
      }
      var term = $(this).prop('value');
      var value = '';
      if (term.match(/:/)) {
        term = term.split(':');
        if (term[0] == 'object') {
          type = term[1];
          if (term[1] == '8321') {
            value += 'gemname';
          } else if (term[1] == '8322') {
            value += 'id';
          }
        } else if (term[0] == 'string') {
          value += term[1];
        } else {
          alert('Error: undefind term, field or operation unknown.');
        }
      } else {
        if (type == '8321') {
          value += "'%" + term + "%'";
        } else if (type == '8322') {
          value += term;
        } else {
          alert('Error: undefind term, value cannot be read.');
        }
      }
      string += value;
    });
  });

  console.log(string);

  query.get(zsld.MAP, lyrs, undefined, 5, true, undefined, undefined, undefined, undefined, string);
  //"id = 4006 or id = 4146 or gemname ilike '%Oberkulm%'"

}




// Initialize the location marker
/*
var element = document.createElement('div');
element.setAttribute('id', 'marker');
element.setAttribute('title', 'ICON');
element.classList.add('marker');
var popup = new ol.Overlay({
  positioning:'bottom-center',
  element: element 
});
zsld.MAP.addOverlay(popup);
*/

// Initialize the suggestion engine
var mySource = new Bloodhound({
  limit: 30,
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: GeoAdmin.serviceUrl + '/rest/services/api/SearchServer?sr=2056&searchText=%QUERY&type=locations',
    filter: function(locations) {
      return locations.results;
    }
  }
});

// This kicks off the loading and processing of local and prefetch data,
// the suggestion engine will be useless until it is initialized
mySource.initialize();

// Initialize typeahead input
$('#search').typeahead(null, {
  name: 'locations',
  displayKey: function(location) {
    return location.attrs.label.replace('<b>', '').replace('</b>', '');
  },
  source: mySource.ttAdapter(),
  templates: {
    suggestion: function(location) {
      return '<p>' + location.attrs.label + '</p>';
    }
  }
});

var parseExtent = function(stringBox2D) {
  var extent = stringBox2D.replace('BOX(', '').replace(')', '').replace(',', ' ').split(' ');
  return $.map(extent, parseFloat);
};

// When a result is selected.
$('#search').on('typeahead:selected', function(evt, location, suggName) {
  console.log('search results: ', location);

  // Hide the community grid layers on the map
  zsld.GemeindeLayer.setVisible(false);

  var originZoom = {
    address: 10,
    parcel: 10,
    sn25: 8,
    feature: 7
  };
  if (!(location.attrs.geom_st_box2d || location.attrs.x || location.attrs.y)) {
    alert("GeoAdmin's addresses service is protected. The Swiss cantons only allow websites of the federal government to use the addresses search service. Please try a LESS DETAILED LOCATION or contact us: geodata@swisstopo.ch");
    return;
  }
  var view = zsld.MAP.getView();
  var origin = location.attrs.origin;
  var extent = [0, 0, 0, 0];
  if (location.attrs.geom_st_box2d) {
    extent = parseExtent(location.attrs.geom_st_box2d);
  } else if (location.attrs.x && location.attrs.y) {
    var x = location.attrs.x;
    var y = location.attrs.y
    extent = [x, y, x, y];
  }

  if (originZoom.hasOwnProperty(origin)) {
    var zoom = originZoom[origin];
    var center = [(extent[0] + extent[2]) / 2, (extent[1] + extent[3]) / 2]; //Math.round()

    // Set style
    $.ajax({
      type: 'GET',
      url: getUrl('style-url'),
      success: function(data) {
        console.log('styles: ', data);
        var olStyleForVector = new ga.style.StylesFromLiterals(data);
        zsld.olVector.setStyle(function(feature) {
          return [olStyleForVector.getFeatureStyle(feature)];
        });
      },
      error: function() {
        failed('');
      }
    });

    // Add features
    $.ajax({
      type: 'GET',
      url: getUrl('feature-yha-url'),
      success: function(data) {

        // Prepare data 
        data.features[0].geometry.coordinates[0] = center[0];
        data.features[0].geometry.coordinates[1] = center[1];
        data.features[0].properties.title = location.attrs.label;
        data.features[0].properties.description = '<b>Koordinaten</b></br>x: ' + location.attrs.x + '</br>y: ' + location.attrs.y;

        console.log('feature: ', data);

        zsld.olSource.clear();
        zsld.olSource.addFeatures(
          zsld.olGeoJSONParser.readFeatures(data)
        );
      },
      error: function() {
        zsld.olSource.clear();
        failed('');
      }
    });


    // Only one vector layer is added
    if (hasVectorOnMap()) {
      zsld.MAP.removeLayer(zsld.olVector);
    }

    // Add GeoJSON layer
    zsld.MAP.addLayer(zsld.olVector);


    view.setZoom(zoom);
    view.setCenter(center);
    //popup.setPosition(center);
  } else {
    // popup.setPosition([0,0]);
    view.fitExtent(extent, zsld.MAP.getSize());
  }
});


addLayer();
