// Load Maps
function ga_loadMaps() {
  $http = new Rest(
    function(data) {
      console.info('GeoAdmin layer successfully loaded ', data);
      if (!$("#ga-layers").length) {
        var title = '<label class="ga-layer-title">Details der Karten</label>';
        $("#ga-layers").append(title);
      }
      for (var i = 0; i < data.properties.length; i++) {
        if (!$("#" + data.properties[i].key).length) {
          var layer = '';
          layer += '<div id="ga-layer-' + data.properties[i].key + '" class="form-check">';
          layer += '    <input class="form-check-input" type="checkbox" value="' + data.properties[i].key + ':' + data.properties[i].file + '" id="' + data.properties[i].key + '" ' + (data.properties[i].visible == 'true' ? 'checked' : '') + ' ' + (data.properties[i].active == 'true' ? '' : 'disabled') + '>';
          layer += '    <label class="form-check-label" for="' + data.properties[i].key + '">' + data.properties[i].description + '</label>'
          layer += '</div>';
          $("#ga-layers").append(layer);
          if (data.properties[i].active == 'true' || data.properties[i].visible == 'true') {
            zsld.LAYERS.add(data.properties[i].key, { map: data.properties[i].file, visible: (data.properties[i].visible == 'true' ? true : false) });
          }
          $("#" + data.properties[i].key).change(function() {
            console.debug('GeoAdmin layer ' + $(this).attr('id') + ' changed to visible ', $(this).is(':checked'));
            zsld.LAYERS.isActiveOnMap($(this).attr('id'), $(this).is(':checked'));
          });
        }
      }
      passed('GeoAdmin layer successfully loaded');
    },
    function(data) {
      console.error('Error attempting to load GeoAdmin layer ', data);
      failed('Error attempting to load GeoAdmin layer');
    }
  );
  $http.get(URL_GA_SETTINGS);
}

ga_loadMaps();
