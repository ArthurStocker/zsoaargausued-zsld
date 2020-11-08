<?php
chdir("..");

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");



/**
 * Login service
 * 
 * Formular und Funktions definitionen
 */
$url = $_SERVER['SCRIPT_NAME'];

$debug = '';
$message = '';

$loginForm = '';
$loginForm .= '<div class="form-group">';
$loginForm .= '    <label for="username">User ID</label>';
$loginForm .= '    <input id="username" name="username" type="text" class="form-control" aria-describedby="userHelp" autocomplete="name" placeholder="Benutzername">';
$loginForm .= '    <small id="userHelp" class="form-text text-muted">Bitte gebe deine Benutzername ein.</small>';
$loginForm .= '</div>';
$loginForm .= '<div class="form-group">';
$loginForm .= '    <label for="password">Password</label>';
$loginForm .= '    <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" placeholder="Passwort">';
$loginForm .= '</div>';

$logoutForm = '';
$logoutForm .= '<div class="form-group">';
$logoutForm .= '    <input id="logout" name="logout" type="checkbox" class="form-control" checked style="visibility: hidden;">';
$logoutForm .= '</div>';

$formAction = '';
$formAction .= '<script>';
$formAction .= '    $("#zsld-login-form").attr("action", "' . $url . '");';
$formAction .= '    $("#zsld-login-form").submit(function(e) {';
$formAction .= '        e.preventDefault();';
$formAction .= '        var url=$(this).closest("form").attr("action");';
$formAction .= '        var data=$(this).closest("form").serialize();';
$formAction .= '        $http_login.post(url, data);';
$formAction .= '    });';
$formAction .= '</script>';

$closeButton = '';
$closeButton .= '<script>';
$closeButton .= '    $("#zsld-login-button-cancel").hide();';
$closeButton .= '    if ($("#zsld-login-button-ok").attr("type") == "submit")';
$closeButton .= '        $("#zsld-login-button-ok").text("Schliessen").removeAttr("type").attr("data-dismiss", "modal").data("dismiss", "modal").toggleClass("btn-success", false).toggleClass("btn-default", true);';
$closeButton .= '</script>';

$loginButton = '';
$loginButton .= '<script>';
$loginButton .= '    $("#modal-login").on("hide.bs.modal", function (e) {';
$loginButton .= '        $("#zsld-login-form").trigger("reset");';
$loginButton .= '    });';
$loginButton .= '    $("#zsld-login-button-cancel").show();';
$loginButton .= '    if ($("#zsld-login-button-ok").attr("data-dismiss") == "modal" || $("#zsld-login-button-ok").text() == "Ausloggen")';
$loginButton .= '        $("#zsld-login-button-ok").text("Einloggen").attr("type", "submit").removeAttr("data-dismiss").removeData("dismiss").toggleClass("btn-success", true).toggleClass("btn-default", false);';
$loginButton .= '</script>';

$logoutButton = '';
$logoutButton .= '<script>';
$logoutButton .= '    $("#modal-login").on("hide.bs.modal", function (e) {';
$logoutButton .= '        $("#zsld-login-form").trigger("reset");';
$logoutButton .= '    });';
$logoutButton .= '    $("#zsld-login-button-cancel").show();';
$logoutButton .= '    if ($("#zsld-login-button-ok").attr("data-dismiss") == "modal" || $("#zsld-login-button-ok").text() == "Einloggen")';
$logoutButton .= '        $("#zsld-login-button-ok").text("Ausloggen").attr("type", "submit").removeAttr("data-dismiss").removeData("dismiss").toggleClass("btn-success", true).toggleClass("btn-defaull", false);';
$logoutButton .= '</script>';

DeviceTAC::restore(  DeviceTAC::read( 'expiration' ) );

if ( !DeviceTAC::read( 'auth' ) ) {
    $userForm = $loginForm . $formAction . $loginButton;

    if ( isset($_POST['username']) && $_POST['username'] != "" && isset($_POST['password']) && $_POST['password'] != "" ) {
        /**
         * Kontrolle, ob der Benutzername und das Passwort korrekt sind.
         */

        //$debug .= '<script>';
        //$debug .= '    console.debug("Device, username and password: ", "' . DEVICE_TAC . '", "' . $_POST['username'] . '", "' . password_hash($_POST['password'],  PASSWORD_DEFAULT) . '");';
        //$debug .= '</script>';

        if ( DeviceTAC::read( 'user' )['data'] === $_POST['username'] ) {
            /**
             * Der Benutzername ist korrekt.
             */

            if ( password_verify( $_POST['password'], DeviceTAC::read( 'user' )['properties']['Passwort'] ) ) {
                /**
                 * Das Passwort ist korrekt.
                 */

                if( password_needs_rehash( DeviceTAC::read( 'user' )['properties']['Passwort'], PASSWORD_DEFAULT ) ) {
                    /**
                     * Der Hashalgorithmus des gespeicherten Passworts genügt nicht mehr den aktuellen Anforderungen,
                     * daher sollte es mittels password_hash() neu gehast und anstelle des alten Hashes in
                     * der Datenbank gespeichert werden:
                     */
                    $data = DeviceTAC::read( 'user' );
                    $data['properties']['Passwort'] = password_hash($_POST['password'],  PASSWORD_DEFAULT);

                    //$debug .= '<script>';
                    //$debug .= '    console.debug("Rehashed password: ", "' . DeviceTAC::read( 'user' )['properties']['Passwort'] . '");';
                    //$debug .= '</script>';

                    $response = ObjectStore::save($data, 'access', $data->id, $data->concurrentobjectsallowed, DATA_PATH . (string)constant( "DATASTORE_" . strtoupper( 'access' ) ) . '.json');

                    if ( $response->error ) {
                        /**
                         * Die Datenbank konnte nicht aktualisiert werden.
                         */
                        $message .= '<b>Login fehlgeschlagen. Die Datenbank konnte nicht aktualisiert werden.</b>';
                        $message .= $closeButton;

                        $message .= $debug;

                        die($message);
                    }
                }

                /**
                 * Die Anmeldung war erfolgreich.
                 */
                $message .= '<b>Login erfolgreich</b>';
                $userForm = $closeButton;

                DeviceTAC::write( 'auth', true );

                $message .= '<script>';
                $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
                $message .= '    Plugins.login.toggle("Logout");';
                $message .= '</script>';

            } else {
                /**
                 * Die Anmeldung ist fehlgeschlagen.
                 */
                $message .= '<b>Login fehlgeschlagen. Bitte prüfe die Angaben.</b>';
                
                DeviceTAC::write( 'auth', false );

                $message .= '<script>';
                $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
                $message .= '    Plugins.login.toggle("Login");';
                $message .= '</script>';

            }
        } else {
            /**
             * Der Benutzername ist nicht korrekt.
             * Ev. prüfen ob als System-Benutzer (root, admin) eingelogged wird.
             */

            $message .= '<b>Login fehlgeschlagen.</b>';
            $message .= $closeButton;

            $message .= $debug;

            die($message);
        }

    }

    $message .= $userForm;

    $message .= $debug;

} else {
    $userForm = $logoutForm . $formAction . $logoutButton;

    if ( isset($_POST['logout']) && $_POST['logout'] ) {
        /**
         * Die Abmeldung war erfolgreich.
         */
        $message .= '<b>Logout erfolgreich</b>';
        $userForm = $closeButton;

        DeviceTAC::write( 'auth', false );
        DeviceTAC::commit();

        $message .= '<script>';
        $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
        $message .= '    Plugins.login.toggle("Login");';
        $message .= '</script>';

    } else {
        /**
         * Die Abmeldung ist fehlgeschlagen.
         */
        $message .= '<b>Du bist Eingeloggt</b>';
        
        DeviceTAC::write( 'auth', true );

        $message .= '<script>';
        $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
        $message .= '    Plugins.login.toggle("Logout");';
        $message .= '</script>';

    }

    $message .= $userForm;
    $message .= $debug;
}
 
if ( !DeviceTAC::read( 'auth' ) ) {
    /**
     * Setze den Status zurück auf ausgeloggt.
     */
    DeviceTAC::abort();

    /**
     * Zeige das Anmeldeformular.
     */
    echo $message;

    // Programm wird hier beendet, denn der Benutzer ist noch nicht eingeloggt.
    exit;
} else {
    /**
     * Setze den Status auf eingeloggt.
     */
    DeviceTAC::commit();

    /**
     * Zeige Erfolgsmeldung.
     */
    echo $message;

    // Programm wird hier weitergeführt, denn der Benutzer ist eingeloggt.
}

// hier kommt Programmteil/Datenausgabe für berechtige Benutzer ...
?>