// create plugin
if (new Plugins('search')) {

  // Remove vector layer from map
  Plugins.search.removeLayer = function() {
    zsld.VECTORS.remove('SEARCH', true, true);
  };

  /**
   * Bloodhound suggestion engine
   *
   * @todo 
   */
  // Bloodhound suggestion engine initialization 
  zsld.BLOODHOUND = new Bloodhound({
    limit: 15,
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

  // Initialize typeahead input
  $('#search').typeahead(null, {
    name: 'locations',
    displayKey: function(location) {
      return location.attrs.label.replace('<b>', '').replace('</b>', '');
    },
    source: zsld.BLOODHOUND.ttAdapter(),
    templates: {
      suggestion: function(location) {
        return '<p>' + location.attrs.label + '</p>';
      }
    }
  });

  // When a result is selected
  $('#search').on('typeahead:selected', function(evt, location, suggName) {
    console.debug("[$('#search').on('typeahead:selected'])] result ", location);

    function parseExtent(value) {
      var extent = value.replace('BOX(', '').replace(')', '').replace(',', ' ').split(' ');
      return $.map(extent, parseFloat);
    };

    // Hide the community grid layers on the map
    zsld.LAYERS.isActiveOnMap('GemeindeLayer', false);

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
      $http_search_style = new Rest(
        function(data) {
          console.info('Styles successfully loaded ', data);
          zsld.VECTORS.add('SEARCH', { clear: true, activate: true, overwrite: true }).setStyles(data);
          passed('Styles successfully loaded');
        },
        function(data) {
          console.error('Error attempting to load styles ', data);
          failed('Error attempting to load styles');
        }
      );
      $http_search_style.get(getUrl('style-url'));


      // Add features
      $http_search_feature = new Rest(
        function(data) {
          console.info('Address data successfully loaded ', data);

          var headers = [];
          headers.push('Marker');
          headers.push('KoordinateX');
          headers.push('KoordinateY');
          headers.push('Institution');
          headers.push('Adresse');
          headers.push('PLZ');
          headers.push('Ort');
          headers.push('Name');
          headers.push('Vorname');
          headers.push('Tel');


          var address = [];
          address.push('0');
          address.push(location.attrs.x);
          address.push(location.attrs.y);
          address.push('Adresse');
          address = address.concat(location.attrs.label.replace(/\s(<b>)(\d+)\s/, ',$2,$1').split(','));
          address.push('');
          address.push('');
          address.push('');

          // , %2C
          // ; %3B

          var uri = encodeURIComponent(headers.join(';') + '\n' + address.join(';') + '\n');

          console.debug("[$('#search').on('typeahead:selected'])] address ", address);

          // Prepare data
          for (var i = 0; i < data.features.length; i++) {
            data.features[i].geometry.coordinates[0] = center[0];
            data.features[i].geometry.coordinates[1] = center[1];
            data.features[i].properties.title = location.attrs.label;
            data.features[i].properties.description = '<b>Koordinaten</b></br>x: ' + location.attrs.x + '</br>y: ' + location.attrs.y + '</br></br></br><a class="data-link" href="data:application/octet-stream;charset=utf-8,' + uri + '" download="standort.csv">Als CSV herunterladen</a>';
          }

          console.debug("[$('#search').on('typeahead:selected'])] feature ", data);

          zsld.VECTORS.add('SEARCH').addFeatures(zsld.GEOJSONPARSER.readFeatures(data), { clear: true, activate: true, append: false, overwrite: true });

          passed('Address data successfully loaded');
        },
        function(data) {
          console.error('Error attempting to load address data ', data);
          failed('Error attempting to load address data');
        }
      );
      $http_search_feature.get(getUrl('feature-yha-url'));

      view.setZoom(zoom);
      view.setCenter(center);
    } else {
      view.fitExtent(extent, zsld.MAP.getSize());
    }
  });
}
