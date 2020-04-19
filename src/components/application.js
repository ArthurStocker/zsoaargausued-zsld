/**
 * Application UI API
 * @constructor
 * @description Erstellen von UI:
 *              if (new UI(name)...) { ... }
 * @param {string} name - name
 * @returns {object}
 *
 * @todo
 */
// Define Application UI API
var UI = function(n) {
  this.$get = function(n) {
    var __isNew = false;

    var Component = function() {

    };

    if (!UI) {
      __isNew = true;
      UI[n] = new Component();
    }

    return UI[n];
  };

  return this.$get(n);
};


/**
 * Plugins Class
 * @constructor
 * @description Erstellen von Plugins:
 *              if (new Plugins(name).ui()...) { ... }
 * @param {string} name - name
 * @returns {object}
 * 
 * @todo 
 */
// Define Plugins Class
var Plugins = function(n) {
  this.$get = function(n) {
    var __isNew = false;
    var __name = n;

    var Plugin = function() {
      this.ui = function() {
        return new UI();
      };
      this.name = function() {
        return __name;
      };
      this.plugins = function() {
        return Plugins;
      };
    };

    if (!Plugins[n]) {
      __isNew = true;
      Plugins[n] = new Plugin();
    }

    return Plugins[n];
  };

  return this.$get(n);
};
