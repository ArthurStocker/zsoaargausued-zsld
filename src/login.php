<?php
require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");


require_once 'config/settings.php';


/**
 * Login service
 * 
 * Formular und Funktions definitionen
 */
$url = $_SERVER['SCRIPT_NAME'];

$debug ='';
$failed = '';
$success ='';
$benutzer = '';

$form = '';
$form .= '<div class="form-group">';
$form .= '    <label for="user">User ID</label>';
$form .= '    <input id="user" name="user" type="text" class="form-control" aria-describedby="userHelp" autocomplete="name" placeholder="Vorname Nachname">';
$form .= '    <small id="userHelp" class="form-text text-muted">Bitte gebe deinen Vornamen und Nachnamen ein.</small>';
$form .= '</div>';
$form .= '<div class="form-group">';
$form .= '    <label for="password">Password</label>';
$form .= '    <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" placeholder="Passwort">';
$form .= '</div>';

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
$closeButton .= '        $("#zsld-login-button-ok").text("Schliessen").removeAttr("type").attr("data-dismiss", "modal").data("dismiss", "modal").toggleClass("btn-success").toggleClass("btn-defaullt");';
$closeButton .= '</script>';

$actionButton = '';
$actionButton .= '<script>';
$actionButton .= '    $("#modal-login").on("hide.bs.modal", function (e) {';
$actionButton .= '        $("#zsld-login-form").trigger("reset");';
$actionButton .= '    });';
$actionButton .= '    if ($("#zsld-login-button-ok").attr("data-dismiss") == "modal")';
$actionButton .= '        $("#zsld-login-button-ok").text("Einloggen").attr("type", "submit").removeData("dismiss").removeAttr("data-dismiss");';
$actionButton .= '</script>';

/**
 * Die Angaben über Benutzer und Passwörter einlesen.
 */
if ( file_exists(DATA_PATH  . 'registration-users-db.txt') ) {
    /**
     * Die Benutzer und Passwürter werden i.d.R. aus einer Datenbank ausgelesen.
     */
    if ( !( $benutzer = json_decode( file_get_contents(DATA_PATH  . 'registration-users-db.txt'), TRUE ) ) ) {

        $failed .= '<b>Login failed. Die Datenbank kann nicht gelesen werden.</b>';
        $failed .= $closeButton;

        die($failed);
    }
} else {
    /**
     * Die Datenbank existiert nicht oder enthält keine der benötigten Angaben.
     * Daher soll sie mit den standart Werten befüllt werden.
     */
    $benutzer = array(
        'root' => '$2y$10$wjCBR6aj4lbDB2wpmrQLKeJEmkJtI6Hs5JMzpDtJdNnD1uD2rmEaC', 
        'admin' => '$2y$10$l0skWinDdGlZBF9/tp7HrOXgFqizNKwXd.jItVQ5AcLBSkLaYOzIe'
    );
    if ( !file_put_contents( DATA_PATH  . 'registration-users-db.txt', json_encode( $benutzer, JSON_PRETTY_PRINT ) ) ) {

        $failed .= '<b>Login fehlgeschlagen. Die Datenbank existiert nicht und wurde zurückgesetzt.</b>';
        $failed .= $closeButton;

        die($failed);
    }
}
 
if ( isset($_POST['user']) && $_POST['user'] != "" && isset($_POST['password']) && $_POST['password'] != "" ) {
    /**
     * Kontrolle, ob User und Password korrekt sind.
     */

    $debug .= '<script>';
    $debug .= '    console.debug("Device, user and password: ", "' . DEVICE_TAC . '", "' . $_POST['user'] . '", "' . password_hash($_POST['password'],  PASSWORD_DEFAULT) . '");';
    $debug .= '</script>';

    if ( !isset( $benutzer[$_POST['user']] ) ) {

        $failed .= '<b>Login fehlgeschlagen.</b>';
        $failed .= $closeButton;

        $failed .= $debug;

        die($failed);
    }

    if ( password_verify( $_POST['password'], $benutzer[$_POST['user']] ) ) {
        /**
         * Die Anmaeldung war erfolgreich.
         */

        DeviceTAC::restore(60 * 60 * 24);

        if( password_needs_rehash( $benutzer[$_POST['user']], PASSWORD_DEFAULT ) ) {
           /**
            *  Der Hashalgorithmus des gespeicherten Passworts genügt nicht mehr den aktuellen Anforderungen,
            *  daher sollte es mittels password_hash() neu gehast und anstelle des alten Hashes in
            *  der Datenbank gespeichert werden, hier wird es nur in der entsprechenden Variable geändert:
            */
            $benutzer[$_POST['user']] = password_hash($_POST['password'],  PASSWORD_DEFAULT);

            $debug .= '<script>';
            $debug .= '    console.debug("Rehashed password: ", "' . $benutzer[$_POST['user']] . '");';
            $debug .= '</script>';

            if (!file_put_contents( DATA_PATH  . 'registration-users-db.txt', json_encode( $benutzer, JSON_PRETTY_PRINT ) ) ) {

                $failed .= '<b>Login fehlgeschlagen. Die Datenbank konnte nicht aktualisiert werden.</b>';
                $failed .= $closeButton;

                $failed .= $debug;

                die($failed);
            }
        }

        $success .= '<b>Login erfolgreich</b>';

        DeviceTAC::write( 'user', $_POST['user'] );
        if ( DeviceTAC::read( 'user' ) === 'root' ) {
            DeviceTAC::write( 'adm', 1);
        } elseif ( DeviceTAC::read( 'user' ) === 'admin' ) {
            DeviceTAC::write( 'adm', 2);
        } else {
            DeviceTAC::write( 'adm', 4);
        }

    } else {
        /**
         * Die Anmeldung ist fehlgeschlagen.
         */

        $failed .= '<b>Login fehlgeschlagen. Bitte prüfe die Angaben.</b>';
        
        DeviceTAC::write( 'adm', 0);
    }
}
 
if ( DeviceTAC::read( 'adm' ) > 0 ) {
    /**
     * Setze den Status auf eingeloggt.
     */
    DeviceTAC::commit();

    /**
     * Zeige Erfolgsmeldung.
     */
    $login = '';
    $login .= $success;
    $login .= $closeButton;
    
    $login .= $debug;

    echo $login;

    // Programm wird hier weitergeführt, denn der Benutzer ist eingeloggt.
} else {
    /**
     * Setze den Status zurück auf eingeloggt.
     */
    DeviceTAC::abort();

    /**
     * Zeige das Anmeldeformular.
     */
    $login = '';
    $login .= $failed;
    $login .= $form;
    $login .= $formAction;
    $login .= $actionButton;

    $login .= $debug;

    echo $login;

    // Programm wird hier beendet, denn der Benutzer ist noch nicht eingeloggt.
    exit;
}

// hier kommt Programmteil/Datenausgabe für berechtige Benutzer ...
?>