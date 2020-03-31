/**
 * ZSLD Layer Api und GeoAdmin Map Layer proxy
 *
 * @todo Initiale visibilität aus den Einstellungen lesen und anwenden
 */
// 
zsld.LAYERS = {};
zsld.VECTORS = {};

// Map layer variable declaration and initialization
zsld.LAYERS.add = function(name, options) {
  if (!this[name] && options && options.map) {
    this[name] = ga.layer.create(options.map);
    zsld.MAP.addLayer(this[name]);
  }
  this.isActiveOnMap(name, options && options.visible);
  return this[name];
};
zsld.LAYERS.remove = function(name, remove) {
  if (this[name]) {
    this[name].setVisible(false);
    if (!!remove && (name != 'add' && name != 'remove' && name != 'isActiveOnMap')) {
      this[name].remove();
      delete this[name];
    }
  }
};
zsld.LAYERS.isActiveOnMap = function(name, visible) {
  if (this[name]) {
    if (!visible) this.remove(name);
    if (!!visible) this[name].setVisible(true);
  } else {
    visible = false;
  }
  return !!visible;
};

// Vector layer variable declaration and initialization
zsld.VECTORS.add = function(name) {
  if (!this[name]) {
    var olSource = new ol.source.Vector();
    var olVector = new ol.layer.Vector({
      source: olSource
    });
    this[name] = {
      layer: olVector,
      source: olSource,
      options: null,
      styles: null,
      features: [],
      setStyles: function(styles, options) {
        if (options) this.options = options;
        if (styles) {
          this.styles = styles;
        }
        console.debug("[{api.js} zsld.VECTORS." + name + ".setStyles] styles ", this.styles, this.options);
        if (!!this.styles) {
          var olStyleForVector = new ga.style.StylesFromLiterals(this.styles);
          this.layer.setStyle(function(feature) {
            return [olStyleForVector.getFeatureStyle(feature)];
          });
        }
      },
      addFeatures: function(features, options) {
        if (options) this.options = options;
        if (options && options.clear) this.remove();
        if (features && (this.features.length == 0 || (this.options && (!!this.options.append || !!this.options.overwrite)))) {
          if (!!this.options.overwrite) {
            this.features = features;
          }
          if (!!this.options.append) {
            this.features = this.features.concat(features);
          }
        }
        console.debug("[{api.js} zsld.VECTORS." + name + ".addFeatures] features ", this.features, this.options);
        if (this.features.length > 0) {
          this.source.addFeatures(
            this.features
          );
        }
        if (options && options.activate) this.setVisible(options.activate);
      },
      setVisible: function(visible) {
        this.layer.setVisible(!!visible);
      },
      remove: function(remove) {
        this.source.clear();
        if (!!remove) this.layer.setMap(null);
      },
    };
    this[name].layer.setMap(zsld.MAP);
    //zsld.MAP.addLayer(this[name].layer);
  }
  return this[name];
};
zsld.VECTORS.remove = function(name, clear, remove) {
  if (this[name]) {
    this[name].setVisible(false);
    if (!!clear) this[name].remove();
    if (!!remove && (name != 'add' && name != 'remove' && name != 'isActiveOnMap')) {
      this[name].remove(remove);
      delete this[name];
    }
  }
};
zsld.VECTORS.isActiveOnMap = function(name, visible, clear) {
  if (this[name]) {
    if (!visible || !!clear) this.remove(name, clear);
    if (!!visible) this[name].setVisible(true);
  } else {
    visible = false;
  }
  return !!visible;
};
