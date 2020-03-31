/*
<div class="form-check">
  <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
  <label class="form-check-label" for="defaultCheck1">
    Default checkbox
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="checkbox" value="" id="defaultCheck2" disabled>
  <label class="form-check-label" for="defaultCheck2">
    Disabled checkbox
  </label>
</div>
*/
// Set style
$http = new Rest(
  function(data) {
    console.info('ZSLD layer successfully loaded ', data);
    var title = '<label>Karten</label>';
    $("#zsld-layers").append(title);
    for (var i = 0; i < data.properties.length; i++) {
      var layer = '';
      layer += '<div class="form-check">';
      layer += '    <input class="form-check-input" type="checkbox" value="' + data.properties[i].key + ':' + data.properties[i].style + '/' + data.properties[i].theme + ':' + data.properties[i].file + '/' + data.properties[i].id + '" id="' + data.properties[i].file + '" ' + (data.properties[i].show == 'true' ? 'checked' : '') + ' ' + (data.properties[i].active == 'true' ? '' : 'disabled') + '>';
      layer += '    <label class="form-check-label" for="' + data.properties[i].file + '">' + data.properties[i].description + '</label>'
      layer += '</div>';
      $("#zsld-layers").append(layer);
      if (data.properties[i].active == 'true') {
        settings_loadStyles(data.properties[i]);
        settings_loadFeatures(data.properties[i]);
      }
    }
    passed('ZSLD layer successfully loaded');
  },
  function(data) {
    console.error('Error attempting to load ZSLD layer ', data);
    failed('Error attempting to load ZSLD layer');
  }
);
$http.get(URL_ZSLD_SETTINGS);

// Load styles
function settings_loadStyles(properties) {
  $http = new Rest(
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
  $http.get(url);
}

// Load features
function settings_loadFeatures(properties) {
  $http = new Rest(
    function(data) {
      console.info('Features for ' + properties.key + ' successfully loaded ', data);
      zsld.VECTORS.add(properties.key).addFeatures(zsld.GEOJSONPARSER.readFeatures(data), { clear: true, activate: properties.show, append: false, overwrite: true });
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
  $http.get(url);
};
