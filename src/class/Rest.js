/**
 * REST Class
 * @constructor
 * @description Erstellen von Endpunkten:
 *              var $http = new Rest(success, error);
 *              $http.get(url, param);
 * @param {string} success - callback
 * @param {string} error - callback
 * @returns {object}
 * 
 * @todo 
 */
// Rest Class
var Rest = function(s, e) {
  var success = '';
  var error = '';
  var type = '';
  var url = '';

  function request() {
    $.ajax({
      url: url,
      type: type,
      success: success,
      error: error
    });
  }

  this.$get = function(s, e) {
    if (s instanceof Function) {
      success = s;
    } else {
      success = function(data) {
        console.info('REST call passed ', data);
        passed('REST call passed');
      }
    }

    if (e instanceof Function) {
      error = e;
    } else {
      error = function() {
        console.error('REST call failed ', data);
        failed('REST call failed');
      }
    }

    var Endpoint = function() {
      this.get = function(u, p) {
        v = {};
        type = 'GET';
        hasParams = false;

        if (p && p.params) {
          for (var i in p.params) {
            if (p.params.hasOwnProperty(i)) {
              hasParams = true;
              if (p.params[i])
                v[i] = p.params[i];
            }
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
  return this.$get(s, e);
}
