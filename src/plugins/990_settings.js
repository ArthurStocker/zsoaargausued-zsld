// create plugin
if (new Plugins('settings')) {

  // Load Maps
  Plugins.settings.loadMaps = function() {
    $http_settings_maps = new Rest(
      function(data) {
        console.info('GeoAdmin layers successfully loaded ', data);
        if (!$("#ga-layers-title").length) {
          var title = '<label id="ga-layers-title">Details der Karten</label>';
          $("#ga-layers").append(title);
        }
        for (var i = 0; i < data.properties.length; i++) {
          if (!$("#" + data.properties[i].key).length) {
            var layer = '';
            layer += '<div id="ga-layers-check-' + data.properties[i].key + '" class="form-check">';
            layer += '    <input id="ga-layer-check-' + data.properties[i].key + '" class="form-check-input" type="checkbox" value="' + data.properties[i].key + '" ' + (data.properties[i].visible == 'true' ? 'checked' : '') + ' ' + (data.properties[i].active == 'true' ? '' : 'disabled') + '>';
            layer += '    <label for="ga-layer-check-' + data.properties[i].key + '" class="form-check-label">' + data.properties[i].description + '</label>';
            layer += '</div>';
            $("#ga-layers").append(layer);
            if (data.properties[i].active == 'true' || data.properties[i].visible == 'true') {
              zsld.LAYERS.add(data.properties[i].key, { map: data.properties[i].file, visible: (data.properties[i].visible == 'true' ? true : false) });
            }
            $("#ga-layer-check-" + data.properties[i].key).change(function() {
              console.debug('GeoAdmin layer ' + $(this).val() + ' changed to visible ', $(this).is(':checked'));
              zsld.LAYERS.isActiveOnMap($(this).val(), $(this).is(':checked'));
            });
          }
        }
        passed('GeoAdmin layers successfully loaded');
      },
      function(data) {
        console.error('Error attempting to load GeoAdmin layers ', data);
        failed('Error attempting to load GeoAdmin layers');
      }
    );
    $http_settings_maps.get(URL_GA_SETTINGS);
  }
}

Plugins.settings.loadMaps();
