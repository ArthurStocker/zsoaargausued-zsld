// Create a background layer
zsld.LAYERS.add('BackgroundLayer', { map: 'ch.swisstopo.pixelkarte-farbe', visible: true });

// Create a coordinate grid CH1903+/LV95 (org.epsg.grid_2056)
zsld.LAYERS.add('KoordinatennetzLayer', { map: 'org.epsg.grid_2056', visible: true });

// Create a community grid layer
zsld.LAYERS.add('GemeindeLayer', { map: 'ch.swisstopo.swissboundaries3d-gemeinde-flaeche.fill', visible: false });













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
          console.info('controller ', olFeature);
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
          console.info('controller ', olFeatures);
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
var controller = (new FeatureFactory()).$get(zsld.MAP, zsld.olSource, zsld.olVector, ((new StyleFactory()).$get()).get('select'), zsld.GEOJSONPARSER);



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
          return console.warn('Missing required parameters');
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
function processor(data) {
  console.info('processor ', data.results);
  if (data.results.length == 1) {
    controller.add(data.results[0], 'object');
  } else if (data.results.length > 1) {
    controller.add(data.results, 'array');
  }
}

//
var query = (new Query()).$get(new Rest(processor), GeoAdmin, null);




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
  zsld.LAYERS.isActiveOnMap('GemeindeLayer', true);

  var lyrs = new ol.Collection();
  lyrs.push(zsld.LAYERS['GemeindeLayer']);

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

  console.debug('execQuery ', string);

  query.get(zsld.MAP, lyrs, undefined, 5, true, undefined, undefined, undefined, undefined, string);
  //"id = 4006 or id = 4146 or gemname ilike '%Oberkulm%'"

}
