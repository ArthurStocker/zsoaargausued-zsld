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

  // Toggle login
  Plugins.login.toggle = function(e) {
    var icon = $("<i></i>");
    if (e == "Login") {
      icon.toggleClass("fas fa-sign-in-alt fa-1x", true);
    } else {
      icon.toggleClass("fas fa-sign-out-alt fa-1x", true);
    }
    $("#modal-label-login").text(e);
    $("#nav-action-login").text(" " + e);
    $("#nav-action-login").prepend(icon);
    $("#nav-item-login").text("");
    $("#nav-item-login").prepend(icon.clone());
  }

  if (!AUTH) {
    Plugins.login.toggle("Login");
  } else {
    Plugins.login.toggle("Logout");
  }
  $('#modal-login').on('show.bs.modal', Plugins.login.exec);
}
