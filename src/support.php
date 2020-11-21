<?php
$GLOBALS["SUPPORT"] = [];

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");


require_once 'config/setup.php';

$setup = new Setup();


if (defined("ERROR")) {

} else {
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta name="viewport" content="initial-scale=0.5, maximum-scale=1, user-scalable=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="manifest" href="<?php echo MANIFEST;?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo ICON_APPLE;?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo ICON_32;?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo ICON_16;?>">
        <!--<link rel='stylesheet' href='https://map.geo.admin.ch/master/25078c3/1809261047/1809261047/style/app.css'>-->
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Libre+Baskerville:400,400italic'>
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/searchpanes/1.0.1/css/searchPanes.dataTables.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css'>
        <link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css'>

        <?php echo '        <link rel="stylesheet" href="'. COMPONENT_PATH . '/' . 'application.css' . '?version=' . time() . '">'; ?>
    </head>

    <body>
        <script src='https://use.fontawesome.com/releases/v5.4.1/js/all.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.2/jquery.xdomainrequest.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/dist/typeahead.bundle.js'></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/searchpanes/1.0.1/js/dataTables.searchPanes.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>

        <!-- Wrapper -->
        <div id="wrapper">
            <div style="margin: 5px;" class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Support und Konfiguration</h3>
                </div>
                <div class="panel-body" style="height: calc(100vh - 58px);">
                    <?php
                    echo DeviceTAC::redirect()->location . "&nbsp" . DeviceTAC::redirect()->params . "&nbsp" . DeviceTAC::redirect()->uri;
                    ?>
                    <!-- Tabs Container -->
                    <div id="support-container" lass="form-horizontal collapse in" aria-expanded="true" style="">
                        <!-- Nav tabs -->
                        <ul id="zsld-support-tab-nav" class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a id="zsld-support-action-request" href="#zsld-support-tab-request" class="active" aria-controls="zsld-support-tab-request" role="tab" data-toggle="tab"  data-list="zsld-support-list-request" data-url="' + last_url + '">Supportanfrage</a>
                            </li>
                            <li role="presentation" class="">
                                <a id="zsld-support-action-settings" href="#zsld-support-tab-settings" class="" aria-controls="zsld-support-tab-settings" role="tab" data-toggle="tab"  data-list="zsld-support-list-settings" data-url="' + last_url + '">Einstellungen</a>
                            </li>
                            <?php
                            /**
                             * Tabs if logged-in
                             */
                            if ( DeviceTAC::read( 'auth' ) ) {
                            ?>
                            <li role="presentation" class="">
                                <a id="zsld-support-action-user" href="#zsld-support-tab-user" class="" aria-controls="zsld-support-tab-user" role="tab" data-toggle="tab"  data-list="zsld-support-list-user" data-url="' + last_url + '">Benutzeradministration</a>
                            </li>
                            <?php
                            }
                            /**
                             * /# Tabs if logged-in
                             */
                            ?>
                        </ul>
                        <!-- /# Nav tabs -->
                        <!-- Tab panes -->
                        <div id="zsld-support-tab-pane" class="tab-content">
                            <?php
                            /**
                             * Tabpanes if DEVICE_TAC is set and the user is logged-in
                             */
                            if ( DeviceTAC::isValid() && DeviceTAC::read( 'auth' ) ) {
                            ?>
                                <!-- Tab pane 'request' -->
                                <div id="zsld-support-tab-request" role="tabpanel" class="tab-pane active">
                                    <div id="zsld-support-request" class="form-check"><br />
                                        <form id="zsld-support-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
                                            <div class="panel panel-default">
                                                <!-- Default panel contents -->
                                                <div class="panel-heading">
                                                    Supportanfrage
                                                </div>
                                                <div class="panel-body">
                                                    <?php
                                                    /**
                                                     * 
                                                     * 
                                                     */
                                                    if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
                                                        ?>
                                                        <div class="panel panel-default">
                                                            <!-- Default panel contents -->
                                                            <div class="panel-heading">
                                                                Angaben zum übermittelten DEVICE_TAC 
                                                            </div>
                                                            <div class="panel-body">
                                                                <?php
                                                                echo "<pre>" . json_encode( $_POST, JSON_PRETTY_PRINT ) . "</pre>\n";
                                                                if ( isset( $_POST['selecteddevicetac'] ) && $_POST['selecteddevicetac'] != "" ) {
                                                                    if ( $devicesdetails = json_decode( file_get_contents( DATA_PATH  . 'support.txt' ), TRUE ) ) {
                                                                        foreach ( array_keys( $devicesdetails ) as $devicedetails ) {
                                                                            if ( $devicedetails != "0" && $devicedetails == $_POST['selecteddevicetac'] ) { 
                                                                                echo "<pre>" . $devicesdetails[$devicedetails]  . "</pre><br />\n";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        $delete = FALSE;
                                                        echo "<br /><pre>Informationen zum DEVICE_TAC: " . constant("DEVICE_TAC") . "<br />\n";
                                                        echo "" . str_replace(array("<", ">"), array("\<", "\>"), json_encode($GLOBALS["SUPPORT"] , JSON_PRETTY_PRINT)) . "</pre><br />\n";
                                                        ?>
                                                        <div class="form-group">
                                                            <label for="zsld-support-select-selecteddevicetac">Übermittelte DEVICE_TAC</label>
                                                            <select class="form-control" id="zsld-support-select-selecteddevicetac" name="selecteddevicetac">
                                                                <?php
                                                                $cases = [];
                                                                if ( $cases = json_decode( file_get_contents( DATA_PATH  . 'support.txt' ), TRUE ) ) {
                                                                    foreach ( array_keys( $cases ) as $case ) {
                                                                        if ( $case != "0" ) {
                                                                            echo "<option>" . $case . "</option>" . "\n";
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "<b>Fehler: Die Supportanfrage konnte nicht gespeichert werden! Die Datenbank existiert nicht.</b><br />\n";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item">
                                                                Geräteangaben nach dem Einlesen löschen
                                                                <label for="zsld-support-checkbox-closerequest" class="switch ">
                                                                    <input id="zsld-support-checkbox-closerequest" name="closerequest" aria-label="Checkbox closerequest" type="checkbox" class="primary" unchecked>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </li>
                                                        </ul>
                                                        <button id="zsld-support-button-submit-closerequest" class="btn btn-success confirm" type="submit">Ausführen</button>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- /# Tab pane 'request' -->
                                <!-- Tab pane 'settings' -->
                                <div id="zsld-support-tab-settings" role="tabpanel" class="tab-pane">
                                    <div id="zsld-support-settings" class="form-check"><br />
                                        <pre>  {settings} </pre>
                                    </div>
                                </div>
                                <!-- /# Tab pane 'settings' -->
                            <?php

                            /**
                             * Tabpanes if DEVICE_TAC is set but not logged-in
                             */
                            } else if ( defined("DEVICE_TAC") ) {
                            ?>
                                <!-- Tab pane 'request' -->
                                <div id="zsld-support-tab-request" role="tabpanel" class="tab-pane active">
                                    <div id="zsld-support-request" class="form-check"><br />
                                        <pre> {} </pre>
                                        <?php
                                        /**
                                         * 
                                         * 
                                         */
                                        if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
                                            ?>
                                            <div class="panel panel-default">
                                                <!-- Default panel contents -->
                                                <div class="panel-heading">
                                                    Supportanfrage 
                                                </div>
                                                <div class="panel-body">
                                                    <?php
                                                    echo "<pre>" . json_encode( $_POST, JSON_PRETTY_PRINT ) . "</pre>\n";
                                                    if ( isset( $_POST['request'] ) && $_POST['request'] == "on" ) {
                                                        $requests = [];
                                                        if ( $requests = json_decode( file_get_contents( DATA_PATH  . 'support.txt' ), TRUE ) ) {
                                                            $requests[constant("DEVICE_TAC")] =  str_replace(array("<", ">"), array("\<", "\>"), json_encode($GLOBALS["SUPPORT"] , JSON_PRETTY_PRINT)) . "\n";
                                                            if ( !file_put_contents( DATA_PATH  . 'support.txt', json_encode( $requests, JSON_PRETTY_PRINT ) ) ) {
                                                                echo "<b>Fehler: Die Supportanfrage konnte nicht gespeichert werden!</b><br />\n";
                                                                $requests = '';
                                                            } else {
                                                                echo "<b>Die Supportanfrage wurde gespeichert.</b><br />\n";
                                                            }
                                                        } else {
                                                            echo "<b>Fehler: Die Supportanfrage konnte nicht gespeichert werden! Die Datenbank existiert nicht.</b><br />\n";
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <form id="zsld-support-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
                                                <div class="panel panel-default">
                                                    <!-- Default panel contents -->
                                                    <div class="panel-heading">
                                                        Supportanfrage
                                                    </div>
                                                    <div class="panel-body">
                                                        <?php
                                                        echo "<pre>Dein DEVICE_TAC ist: " . constant("DEVICE_TAC") . "</pre><br />\n";
                                                        ?>
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item">
                                                                Geräteangaben speichern und Supportanfrage senden
                                                                <input id="zsld-support-checkbox-request" name="request" aria-label="Checkbox request" type="checkbox" class="switch" unchecked>
                                                                <label for="zsld-support-checkbox-request"></label>
                                                            </li>
                                                        </ul>
                                                        <button id="zsld-support-button-submit-request" class="btn btn-success confirm" type="submit">Senden</button>
                                                    </div>
                                                </div> 
                                            </form>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- /# Tab pane 'request' -->
                                <!-- Tab pane 'settings' -->
                                <div id="zsld-support-tab-settings" role="tabpanel" class="tab-pane">
                                    <div id="zsld-support-settings" class="form-check"><br />
                                        <pre> {} </pre>
                                        <?php
                                            $keys = [];
                                            if ( $keys = json_decode( file_get_contents( DATA_PATH  . 'registration.txt' ) ) ) {
                                                if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
                                                    ?>
                                                    <div class="panel panel-default">
                                                        <!-- Default panel contents -->
                                                        <div class="panel-heading">
                                                            Einstellungen Registrationsdialog
                                                        </div>
                                                        <div class="panel-body">
                                                            <?php
                                                            echo "<pre>" . json_encode( $_POST, JSON_PRETTY_PRINT ) . "</pre>\n";
                                                            if ( isset( $_POST['enabledialog'] ) && $_POST['enabledialog'] == "on" ) {
                                                                $keys[] = constant("DEVICE_TAC");
                                                                echo "<b>Das Gerät wurde zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
                                                            } 
                                                            if ( isset( $_POST['disabledialog'] ) && $_POST['disabledialog'] == "on" ) {
                                                                if ( in_array( DEVICE_TAC, $keys ) ) {
                                                                    foreach ( array_keys( $keys, constant("DEVICE_TAC") ) as $key ) {
                                                                        unset( $keys[$key] );
                                                                    }
                                                                }
                                                                echo "<b>Das Gerät ist nicht länger zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
                                                            }
                                                            if ( !file_put_contents( DATA_PATH  . 'registration.txt', json_encode( $keys, JSON_PRETTY_PRINT ) ) ) {
                                                                echo "<b>Fehler: Die Einstellungen konnten nicht gespeichert werden!</b><br />\n";
                                                                $keys = '';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <form id="zsld-support-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
                                                        <div class="panel panel-default">
                                                            <!-- Default panel contents -->
                                                            <div class="panel-heading">
                                                                Einstellungen Registrationsdialog
                                                            </div>
                                                            <div class="panel-body">
                                                                <ul class="list-group list-group-flush">
                                                                    <?php
                                                                        if ( in_array( DEVICE_TAC, $keys ) ) {
                                                                            ?>
                                                                            <li class="list-group-item">
                                                                                Das Gerät ist zur Anzeige des Registrationsdialoges vorgemerkt. Registrationsdialog deaktivieren
                                                                                <input id="zsld-support-checkbox-disabledialog-on" type="radio" name="disabledialog" value="on" checked>
                                                                                <label for="sld-support-checkbox-disabledialog-on">ja</label>
                                                                                <input id="zsld-support-checkbox-disabledialog-off" type="radio" name="disabledialog" value="off">
                                                                                <label for="zsld-support-checkbox-disabledialog-off">nein</label>
                                                                            </li>
                                                                            <?php
                                                                        } else {
                                                                            ?>
                                                                            <li class="list-group-item">
                                                                                Das Gerät ist nicht zur Anzeige des Registrationsdialoges vorgemerkt. Registrationsdialog aktivieren
                                                                                <input id="zsld-support-checkbox-enabledialog-on" type="radio" name="enabledialog" value="on" checked>
                                                                                <label for="zsld-support-checkbox-enabledialog-on">ja</label>
                                                                                <input id="zsld-support-checkbox-enabledialog-off" type="radio" name="enabledialog" value="off">
                                                                                <label for="zsld-support-checkbox-enabledialog-off">nein</label>
                                                                            </li>
                                                                            <?php
                                                                        }
                                                                    ?>
                                                                </ul>
                                                                <button id="zsld-support-button-submit-enabledialog" class="btn btn-success confirm" type="submit">Speichern</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <?php
                                                }
                                            }  else {
                                                echo "<b>Fehler: Die Einstellungen konnten nicht gespeichert werden! Die Datenbank existiert nicht.</b><br />\n";
                                            }
                                        ?>
                                    </div>
                                </div>
                                <!-- /# Tab pane 'settings' -->
                            <?php

                            /**
                             * Tabpanes if DEVICE_TAC is missing
                             */
                            } else {
                            ?>
                                <!-- Tab pane 'request' -->
                                <div id="zsld-support-tab-request" role="tabpanel" class="tab-pane active">
                                    <div id="zsld-support-request" class="form-check"><br />
                                        <pre>Fehler: kein DEVICE_TAC definiert.</pre>
                                    </div>
                                </div>
                                <!-- /# Tab pane 'request' -->
                                <!-- Tab pane 'settings' -->
                                <div id="zsld-support-tab-settings" role="tabpanel" class="tab-pane">
                                    <div id="zsld-support-settings" class="form-check"><br />
                                        <pre>Fehler: kein DEVICE_TAC definiert.</pre>
                                    </div>
                                </div>
                                <!-- /# Tab pane 'settings' -->
                            <?php
                            }
    
                            /**
                             * Tabpanes if logged-in
                             */
                            if ( DeviceTAC::read( 'auth' ) ) {
                            ?>
                                <!-- Tab pane 'user' -->
                                <div id="zsld-support-tab-user" role="tabpanel" class="tab-pane">
                                    <div id="zsld-support-user" class="form-check"><br />
                                        <!--
                                        <label id="zsld-support-title-user" class="form-check-label" >
                                            <div class="btn-group btn-group-xs" role="group" aria-label="...">
                                                <button id="zsld-support-switch-user-last" type="button" class="btn btn-default" data-list="zsld-support-list-user" data-url="' + last_url + '">Letzte Bewegungen</button>
                                                <button id="zsld-support-switch-user-all" type="button" class="btn btn-default" data-list="zsld-support-list-user" data-url="' + all_url + '">Alle Bewegungen</button>
                                            </div>
                                        </label>
                                        <table id="zsld-support-list-user" class="table table-striped table-bordered" style="width:100%"></table>
                                        -->

                                        <?php 
                                            //echo $_SERVER["REQUEST_METHOD"] . "<br />\n";
                                            $password = randomPassword();
                                            echo "<pre>Password      '" . $password . "'<br />Password Hash '" . password_hash($password,  PASSWORD_DEFAULT) . "'</pre>";
                                        ?>

                                    </div>
                                </div>
                                <!-- /# Tab pane 'user' -->
                            <?php
                            }

                            /**
                             * 
                             */
                            ?>
                        </div>
                        <!-- /# Tab panes -->
                    </div>
                    <!-- /# Tabs Container -->
                </div>
            </div>
        </div>
        <!-- /# Wrapper -->

    </body>

</html>
<?php
}

function randomPassword() {
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = 7;
    $d = rand(0, $alphaLength);
    do {
        $s = rand(0, $alphaLength);
    } while ( $s === $d );
    for ($i = 0; $i < 8; $i++) {
        if ( $i === $d ) {
            $pass[] = randomNumber();
        } else if ( $i === $s ) {
            $pass[] = randomSpecChar(); 
        } else {
            if ( rand(0, 1) === 1 )  {
                $pass[] = randomUpper();
            } else {
                $pass[] = randomLower();
            }

        }
    }
    return implode($pass); //turn the array into a string
}

function randomUpper() {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}
function randomLower() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyz';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}

function randomNumber() {
    $alphabet = '1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}

function randomSpecChar() {
    $alphabet = '+*%&$?';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}
?>