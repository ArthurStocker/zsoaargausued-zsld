// create plugin
if (new Plugins('geojson')) {
  // Load styles
  Plugins.geojson.loadStyles = function(editor_only) {
    $http_geojson_style = new Rest(
      function(data) {
        console.info('Styles successfully loaded ', data);
        if (!editor_only) zsld.VECTORS.add('GEOJSON').setStyles(data);
        $('#editor-content').text(JSON.stringify(data, null, 4));
        passed('Styles successfully loaded');
      },
      function(data) {
        console.error('Error attempting to load styles ', data);
        $('#editor-content').text('');
        failed('Error attempting to load styles');
      }
    );
    $http_geojson_style.get(getUrl('style-url'));
  };

  // Apply style changes from editor
  Plugins.geojson.applyStyling = function() {
    var data = JSON.parse($('#editor-content').val());
    zsld.VECTORS.add('GEOJSON').setStyles(data);
    Plugins.geojson.loadFeatures();
  };

  // Load features
  Plugins.geojson.loadFeatures = function() {
    $http_geojson_feature = new Rest(
      function(data) {
        console.info('Features successfully loaded ', data);
        zsld.VECTORS.add('GEOJSON').addFeatures(zsld.GEOJSONPARSER.readFeatures(data), { clear: true, activate: true, append: true, overwrite: false });
        passed('Features successfully loaded');
      },
      function(data) {
        console.error('Error attempting to load features ', data);
        zsld.VECTORS.remove('GEOJSON', true, true);
        failed('Error attempting to load features');
      }
    );
    $http_geojson_feature.get(getUrl('feature-url'));
  };

  // Remove vector layer from map
  Plugins.geojson.removeLayer = function() {
    zsld.VECTORS.remove('GEOJSON', true, true);
  };

  // Apply GeoJSON config from urls
  Plugins.geojson.addLayer = function() {
    Plugins.geojson.loadStyles();
    Plugins.geojson.loadFeatures();
  }
}
