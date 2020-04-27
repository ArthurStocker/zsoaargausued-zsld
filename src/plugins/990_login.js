/**
 * Login Page
 * to show the details of the transactions and
 * set permissions to access the data we have to login
 * and set grants to registered devices and set default
 * timeout before lockout if if the user will not register
 * his device.
 */
// create plugin
if (new Plugins('login')) {

  // Execute login
  Plugins.login.exec = function(e) {
    $http_login = new Rest(
      function(data) {
        console.info('Login response ', data);
        passed('Login response ');
        $('#zsld-login').html(data);
      },
      function(data) {
        console.error('Error attempting to access the data, please login ', data);
        failed('Error attempting to access the data, please login');
        $('#zsld-login').html(data);
      }
    );
    $http_login.get("/map/login");
  }

  $('#modal-login').on('show.bs.modal', Plugins.login.exec);
}
