// create plugin
if (new Plugins('layer')) {

  // Load Maps
  Plugins.layer.loadMaps = function() {
    $http_layer_maps = new Rest(
      function(data) {
        console.info('ZSLD layer successfully loaded ', data);
        if (!$("#zsld-layers-title").length) {
          var title = '<label id="zsld-layers-title">Karten</label>';
          $("#zsld-layers").append(title);
        }
        for (var i = 0; i < data.properties.length; i++) {
          if (!$("#" + data.properties[i].file).length) {
            var layer = '';
            layer += '<div id="zsld-layers-check-' + data.properties[i].key + '" class="form-check">';
            layer += '    <input id="zsld-layer-check-' + data.properties[i].file + '" class="form-check-input" type="checkbox" value="' + data.properties[i].key + ':' + data.properties[i].style + '/' + data.properties[i].theme + ':' + data.properties[i].file + '/' + data.properties[i].id + '" ' + (data.properties[i].visible == 'true' ? 'checked' : '') + ' ' + (data.properties[i].active == 'true' ? '' : 'disabled') + '>';
            layer += '    <label for="zsld-layer-check-' + data.properties[i].file + '" class="form-check-label">' + data.properties[i].description + '</label>';
            layer += '</div>';
            $("#zsld-layers").append(layer);
            if (data.properties[i].active == 'true') {
              Plugins.layer.loadStyles(data.properties[i]);
              Plugins.layer.loadFeatures(data.properties[i]);
            }
            $("#zsld-layer-check-" + data.properties[i].file).change(function() {
              console.debug('ZSLD layer ' + $(this).val().split(':')[0] + ' changed to visible ', $(this).is(':checked'));
              zsld.VECTORS[$(this).val().split(':')[0]].setVisible(!!$(this).is(':checked'));
            });
          }
        }
        passed('ZSLD layer successfully loaded');
      },
      function(data) {
        console.error('Error attempting to load ZSLD layer ', data);
        failed('Error attempting to load ZSLD layer');
      }
    );
    $http_layer_maps.get(URL_ZSLD_SETTINGS);
  }

  // Load styles
  Plugins.layer.loadStyles = function(properties) {
    $http_layer_style = new Rest(
      function(data) {
        console.info('Styles for ' + properties.key + ' successfully loaded ', data);
        zsld.VECTORS.add(properties.key).setStyles(data);
        passed('Styles for ' + properties.key + ' successfully loaded');
      },
      function(data) {
        console.error('Error attempting to load styles for ' + properties.key + ' ', data);
        failed('Error attempting to load styles for ' + properties.key);
      }
    );
    var url = URL_ZSLD_SETTINGS.replace(/(type=)[^&]*/, '$1style');
    url = url.replace(/(object=)[^&]*/, '$1' + properties.style);
    url = url.replace(/(id=).*$/, '$1' + properties.theme);
    console.debug('ZSLD layer style URL ', url);
    $http_layer_style.get(url);
  }

  // Load features
  Plugins.layer.loadFeatures = function(properties) {
    $http_layer_feature = new Rest(
      function(data) {
        console.info('Features for ' + properties.key + ' successfully loaded ', data);
        zsld.VECTORS.add(properties.key).addFeatures(zsld.GEOJSONPARSER.readFeatures(data), { clear: true, activate: (properties.visible == 'true'), append: false, overwrite: true });
        passed('Features for ' + properties.key + ' successfully loaded');
      },
      function(data) {
        console.error('Error attempting to load features for ' + properties.key + ' ', data);
        zsld.VECTORS.remove(properties.key, true, true);
        failed('Error attempting to load features for ' + properties.key);
      }
    );
    var url = URL_ZSLD_SETTINGS.replace(/(type=)[^&]*/, '$1feature');
    url = url.replace(/(object=)[^&]*/, '$1' + properties.file);
    url = url.replace(/(id=).*$/, '$1' + properties.id);
    console.debug('ZSLD layer feature URL ', url);
    $http_layer_feature.get(url);
  };

  // Remove vector layer from map
  Plugins.layer.ResetLayers = function() {
    $("#zsld-layers").children().each(function(index) {
      console.log(index + " " + $(this).attr('id') + ": " + $(this).text());
      zsld.VECTORS.remove(($(this).attr('id')).replace('zsld-layers-check-', ''), true, true);
    });
    $("#zsld-layers").empty();
    Plugins.layer.loadMaps();
  };

  Plugins.layer.loadMaps();
}
