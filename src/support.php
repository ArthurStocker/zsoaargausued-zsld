<?php
$GLOBALS["SUPPORT"] = [];

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");


require_once 'config/setup.php';


$setup = new Setup();












if (defined("ERROR")) {

} else {
    $TEST = FALSE;
    $DEBUG = TRUE;

    require_once 'class/UserRegAndAuth.php';

    $users = new UserRegAndAuth();

    $users->init();
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/v/bs4/jq-3.3.1/jszip-2.5.0/dt-1.10.23/b-1.6.5/b-colvis-1.6.5/b-html5-1.6.5/sb-1.0.1/sp-1.2.2/sl-1.3.1/datatables.min.css"/>

        <?php echo '        <link rel="stylesheet" href="'. COMPONENT_PATH . '/' . 'application.css' . '?version=' . time() . '">'; ?>
    </head>
 

    <body>
        <script src='https://use.fontawesome.com/releases/v5.4.1/js/all.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.2/jquery.xdomainrequest.min.js'></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/dist/typeahead.bundle.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/v/bs4/jq-3.3.1/jszip-2.5.0/dt-1.10.23/b-1.6.5/b-colvis-1.6.5/b-html5-1.6.5/sb-1.0.1/sp-1.2.2/sl-1.3.1/datatables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
        
        <script src="/map/class/Rest.js"></script>

        <!-- Wrapper -->
        <div id="wrapper">
            <div style="margin: 5px;" class="card ">
                <div class="card-header">
                    <h3 class="card-title">Support und Konfiguration</h3>
                </div>
                <div class="card-body" style="height: calc(100vh - 58px);">
                    <!-- Tabs Container -->
                    <div id="support-container" lass="form-horizontal collapse in" aria-expanded="true" style="">
                        <!-- Nav tabs -->
                        <ul id="zsld-support-tab-nav" class="nav nav-tabs" role="tablist">
                            <?php
                            /**
                             * public tabs
                             */
                            ?>
                            <li role="presentation" class="nav-item active">
                                <a id="zsld-support-action-request" href="#zsld-support-tab-request" class="nav-link active" aria-controls="zsld-support-tab-request" role="tab" data-toggle="tab"  data-list="zsld-support-list-request" data-url="' + last_url + '">Supportanfrage</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a id="zsld-support-action-settings" href="#zsld-support-tab-settings" class="nav-link" aria-controls="zsld-support-tab-settings" role="tab" data-toggle="tab"  data-list="zsld-support-list-settings" data-url="' + last_url + '">Einstellungen</a>
                            </li>
                            <?php
                            /**
                             * /# public tabs
                             */
                            /**
                             * private tabs if logged-in
                             */
                            if ( DeviceTAC::read( 'auth' ) ) {
                            ?>
                            <li role="presentation" class="nav-item">
                                <a id="zsld-support-action-user" href="#zsld-support-tab-user" class="nav-link" aria-controls="zsld-support-tab-user" role="tab" data-toggle="tab"  data-list="zsld-support-list-user" data-url="' + last_url + '">Benutzeradministration</a>
                            </li>
                            <?php
                            }
                            /**
                             * /# private tabs if logged-in
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
                                    <div class="card border-top-0">
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <h1 class="display-4">Geräteangaben</h1>
                                            <?php
                                            if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
                                                if ( isset( $_POST['selecteddevicetac'] ) && $_POST['selecteddevicetac'] != "" ) {
                                            ?>
                                                    <p class="lead"><?php echo "zum Gerät " . $_POST['selecteddevicetac'] . ""; ?></p>
                                                </div>
                                            </div>
                                            <?php
                                                    if ( $devicesdetails = json_decode( file_get_contents( DATA_PATH  . 'support.txt' ), TRUE ) ) {
                                                        foreach ( array_keys( $devicesdetails ) as $devicedetails ) {
                                                            if ( $devicedetails != "0" && $devicedetails == $_POST['selecteddevicetac'] ) {
                                            ?>
                                            <div class="accordion" id="deviceInfo">
                                                <div class="card">
                                                    <div id="headingDeviceInfo" class="card-header">
                                                        <h6 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#deviceInfoContent" aria-expanded="true" aria-controls="deviceInfoContent">Details zum Gerät</button>
                                                        </h6>
                                                    </div>

                                                    <div id="deviceInfoContent" class="collapse show" aria-labelledby="headingDeviceInfo" data-parent="#deviceInfo">
                                                        <div class="card-body">
                                                            <pre class="text-black-50"><code><?php echo "" . str_replace(array("<", ">"), array("&lt;", "&gt;"), $devicesdetails[$devicedetails]) . ""; ?></code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                                if ( isset( $_POST['closerequest'] ) && $_POST['closerequest'] == "on" ) {
                                                                    unset( $devicesdetails[$devicedetails] );
                                                                    echo "<br /><b>Die Supportanfrage wurde gelöscht.</b><br />\n";
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if ( !file_put_contents( DATA_PATH  . 'support.txt', json_encode( $devicesdetails, JSON_PRETTY_PRINT ) ) ) {
                                                    echo "<br /><b>Fehler: Die Supportanfrage konnte nicht gelöscht werden!</b><br />\n";
                                                    $devicesdetails = '';
                                                }
                                            ?>
                                            <?php
                                                if ( $DEBUG ) {
                                            ?>
                                            <br />
                                            <pre class="text-danger"><code><?php echo "" . json_encode( $_POST, JSON_PRETTY_PRINT ) . ""; ?></code></pre>
                                            <?php
                                                }
                                            } else {
                                                $delete = FALSE;
                                            ?>
                                                    <p class="lead"><?php echo "zu deinem Gerät " . constant("DEVICE_TAC") . ""; ?></p>
                                                </div>
                                            </div>

                                            <div class="accordion" id="deviceInfo">
                                                <div class="card">
                                                    <div id="headingDeviceInfo" class="card-header">
                                                        <h6 class="mb-0">
                                                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#deviceInfoContent" aria-expanded="true" aria-controls="deviceInfoContent">Details zum Gerät</button>
                                                        </h6>
                                                    </div>

                                                    <div id="deviceInfoContent" class="collapse" aria-labelledby="headingDeviceInfo" data-parent="#deviceInfo">
                                                        <div class="card-body">
                                                            <pre class="text-black-50"><code><?php echo "" . str_replace(array("<", ">"), array("&lt;", "&gt;"), json_encode($GLOBALS["SUPPORT"] , JSON_PRETTY_PRINT)) . ""; ?></code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br />

                                            <form id="zsld-support-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">

                                                <div class="form-group">

                                                    <div class="input-group is-invalid">
                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="zsld-support-select-selecteddevicetac">Übermittelte Geräte</label>
                                                        </div>
                                                        <select id="zsld-support-select-selecteddevicetac" class="custom-select" name="selecteddevicetac" required>
                                                    <?php
                                                    $cases = [];
                                                    if ( $cases = json_decode( file_get_contents( DATA_PATH  . 'support.txt' ), TRUE ) ) {
                                                    ?>
                                                        <?php
                                                            foreach ( array_keys( $cases ) as $case ) {
                                                                if ( $case != "0" ) {
                                                                    echo '<option value="' . $case . '">' . $case . "</option>" . "\n";
                                                                }
                                                            }
                                                        ?>
                                                        </select>
                                                    </div>
                                                    <?php
                                                    } else {
                                                    ?>
                                                            <option value="Fehler">Fehler</option>
                                                        </select>
                                                    </div>
                                                    <div class="invalid-feedback">
                                                    <?php
                                                        echo "Die Supportanfragen konnten nicht geladen werden! Die Datenbank existiert nicht.\n";
                                                    ?>
                                                    </div>
                                                    <?php
                                                    }
                                                    ?>

                                                </div>

                                                <div class="form-group">
                                                    <div id="zsld-form-group-request" class="input-group">
                                                        Geräteangaben nach dem Einlesen
                                                        <div class="custom-control custom-switch">
                                                            <input id="zsld-support-checkbox-closerequest" name="closerequest" type="checkbox" class="custom-control-input" unchecked>
                                                            <label class="custom-control-label" for="zsld-support-checkbox-closerequest">löschen</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button id="zsld-support-button-submit-closerequest" class="btn btn-success confirm" type="submit">Ausführen</button>
                                            </form>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

                                </div>
                                <!-- /# Tab pane 'request' -->
                                <!-- Tab pane 'settings' -->
                                <div id="zsld-support-tab-settings" role="tabpanel" class="tab-pane">
                                    <div class="card border-top-0">
                                        <!-- Default card contents -->
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <h1 class="display-4">SMS Service</h1>
                                                    <p class="lead"><?php echo SMS_URL; ?></p>
                                                </div>
                                            </div>
                                        </div>
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
                                <?php
                                if ( $_SERVER["REQUEST_METHOD"] === "POST" && ( isset( $_POST['request'] ) && $_POST['request'] == "on" ) ) {
                                ?>
                                    <div class="card border-top-0">
                                        <!-- Default card contents -->
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <?php
                                                    if ( isset( $_POST['request'] ) && $_POST['request'] == "on" ) {
                                                    ?>
                                                    <h1 class="display-4">Deine Supportanfrage</h1>
                                                    <?php
                                                        $requests = [];
                                                        if ( $requests = json_decode( file_get_contents( DATA_PATH  . 'support.txt' ), TRUE ) ) {
                                                            $requests[constant("DEVICE_TAC")] =  str_replace(array("<", ">"), array("\<", "\>"), json_encode($GLOBALS["SUPPORT"] , JSON_PRETTY_PRINT)) . "\n";
                                                            if ( !file_put_contents( DATA_PATH  . 'support.txt', json_encode( $requests, JSON_PRETTY_PRINT ) ) ) {
                                                                ?>
                                                                <p class="lead">konnte nicht gespeichert werden!</p>
                                                                <?php
                                                                $requests = '';
                                                            } else {
                                                                ?>
                                                                <p class="lead">wurde gespeichert</p>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <p class="lead">konnte nicht gespeichert werden! Die Datenbank existiert nicht.</p>
                                                            <?php
                                                        }
                                                    } else {
                                                    ?>
                                                        <h1 class="display-4">Deine Gerätenummer</h1>
                                                        <p class="lead"><?php echo "" . constant("DEVICE_TAC") . ""; ?></p>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                            if ( $DEBUG ) {
                                            ?>
                                            <pre class="text-danger"><code><?php echo "" . json_encode( $_POST, JSON_PRETTY_PRINT ) . ""; ?></code></pre>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="card border-top-0">
                                        <!-- Default card contents -->
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <h1 class="display-4">Deine Gerätenummer</h1>
                                                    <p class="lead"><?php echo "" . constant("DEVICE_TAC") . ""; ?></p>
                                                </div>
                                            </div>
                                            <form id="zsld-support-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
                                                <div class="form-group">
                                                    <div id="zsld-form-group-request" class="input-group">
                                                        Geräteangaben speichern und Supportanfrage
                                                        <div class="custom-control custom-switch">
                                                            <input id="zsld-support-checkbox-request" name="request" type="checkbox" class="custom-control-input" unchecked>
                                                            <label class="custom-control-label" for="zsld-support-checkbox-request">senden</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button id="zsld-support-button-submit-request" class="btn btn-success confirm" type="submit">Senden</button>
                                            </form>
                                        </div>
                                    </div> 
                                    <?php
                                }
                                ?>
                                </div>
                                <!-- /# Tab pane 'request' -->
                                <!-- Tab pane 'settings' -->
                                <div id="zsld-support-tab-settings" role="tabpanel" class="tab-pane">
                                    <?php
                                    $keys = [];
                                    $saved = true;
                                    if ( $keys = json_decode( file_get_contents( DATA_PATH  . 'registration.txt' ) ) ) {
                                        if ( $_SERVER["REQUEST_METHOD"] === "POST" && ( ( isset( $_POST['enabledialog'] ) && $_POST['enabledialog'] == "on" ) || ( isset( $_POST['disabledialog'] ) && $_POST['disabledialog'] == "on" ) ) ) {

                                            if ( isset( $_POST['enabledialog'] ) && $_POST['enabledialog'] == "on" ) {
                                                $keys[] = constant("DEVICE_TAC");
                                            } 
                                            if ( isset( $_POST['disabledialog'] ) && $_POST['disabledialog'] == "on" ) {
                                                if ( in_array( DEVICE_TAC, $keys ) ) {
                                                    foreach ( array_keys( $keys, constant("DEVICE_TAC") ) as $key ) {
                                                        unset( $keys[$key] );
                                                    }
                                                }
                                            }
                                            if ( !file_put_contents( DATA_PATH  . 'registration.txt', json_encode( $keys, JSON_PRETTY_PRINT ) ) ) {
                                                $saved = false;
                                                $keys = '';
                                            }

                                        }
                                    ?>
                                    <div class="card border-top-0">
                                        <!-- Default card contents -->
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <h1 class="display-4">Dein Gerät</h1>
                                                    <?php
                                                    if ( in_array( DEVICE_TAC, $keys ) ) {
                                                    ?>
                                                    <p class="lead">ist zur Anzeige des Registrationsdialoges vorgemerkt.</p>
                                                    <?php
                                                    } else {
                                                    ?>
                                                    <p class="lead">ist <b>nicht</b> zur Anzeige des Registrationsdialoges vorgemerkt.</p>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <form id="zsld-support-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
                                            <?php
                                            if ( in_array( DEVICE_TAC, $keys ) ) {
                                            ?>
                                                <div class="form-group">
                                                    <div id="zsld-form-group-disabledialog" class="input-group">
                                                        Registrationsdialog deaktivieren
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input id="zsld-support-checkbox-disabledialog-on" name="disabledialog" type="radio" class="custom-control-input" value="on" checked>
                                                            <label class="custom-control-label" for="zsld-support-checkbox-disabledialog-on">ja</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input id="zsld-support-checkbox-disabledialog-off" name="disabledialog" type="radio" class="custom-control-input" value="off">
                                                            <label class="custom-control-label" for="zsld-support-checkbox-disabledialog-off">nein</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            } else {
                                            ?>
                                                <div class="form-group">
                                                    <div id="zsld-form-group-enabledialog" class="input-group">
                                                        Registrationsdialog aktivieren
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input id="zsld-support-checkbox-enabledialog-on" name="enabledialog" type="radio" class="custom-control-input" value="on" checked>
                                                            <label class="custom-control-label" for="zsld-support-checkbox-enabledialog-on">ja</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input id="zsld-support-checkbox-enabledialog-off" name="enabledialog" type="radio" class="custom-control-input" value="off">
                                                            <label class="custom-control-label" for="zsld-support-checkbox-enabledialog-off">nein</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>

                                                <button id="zsld-support-button-submit-registratondialog" class="btn btn-success confirm" type="submit">Senden</button>
                                            </form>
                                            <?php
                                            if ( !$saved ) {
                                                echo "<b>Fehler: Die Einstellungen konnten nicht gespeichert werden!</b><br />\n";
                                            }
                                            if ( $DEBUG && $_SERVER["REQUEST_METHOD"] === "POST" && ( ( isset( $_POST['enabledialog'] ) && $_POST['enabledialog'] == "on" ) || ( isset( $_POST['disabledialog'] ) && $_POST['disabledialog'] == "on" ) ) ) {
                                            ?>
                                            <br />
                                            <pre class="text-danger"><code><?php echo "" . json_encode( $_POST, JSON_PRETTY_PRINT ) . ""; ?></code></pre>
                                            <?php
                                            }
                                            ?>
                                        </div>                                                
                                    </div>
                                    <?php
                                    }  else {
                                    ?>
                                    <div class="card border-top-0">
                                        <!-- Default card contents -->
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <h1 class="display-4">Die Einstellungen</h1>
                                                    <p class="lead">konnten nicht gespeichert werden! Die Datenbank existiert nicht.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <?php
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
                                    <div class="card border-top-0">
                                        <!-- Default card contents -->
                                        <!--
                                        <div class="card-header">
                                            HEADER
                                        </div>
                                        -->
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <h1 class="display-4">Fehler</h1>
                                                    <p class="lead">kein DEVICE_TAC definiert</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                                <!-- /# Tab pane 'request' -->
                                <!-- Tab pane 'settings' -->
                                <div id="zsld-support-tab-settings" role="tabpanel" class="tab-pane">
                                    <div class="card border-top-0">
                                        <!-- Default card contents -->
                                        <!--
                                        <div class="card-header">
                                            HEADER
                                        </div>
                                        -->
                                        <div class="card-body">
                                            <div class="jumbotron jumbotron-fluid">
                                                <div class="container">
                                                    <h1 class="display-4">Fehler</h1>
                                                    <p class="lead">kein DEVICE_TAC definiert</p>
                                                </div>
                                            </div>
                                        </div>
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

                                        <div class="card border-top-0">
                                            <!-- Default card contents -->
                                            <div class="card-body">
                                                <div class="jumbotron jumbotron-fluid">
                                                    <div class="container">
                                                        <h1 class="display-4">Registrierte Benutzer</h1>
                                                        <p class="lead">bearbeiten der Berechtigungen</p>
                                                        <?php
                                                        if ( $TEST ) {
                                                            $password = $users->randomPassword();
                                                            echo "<p>Password       :'" . $password . "'</p>";
                                                            echo "<p>Password Hash  :'" . password_hash($password,  PASSWORD_DEFAULT) . "'</p>";
                                                            echo "<p>Redirect to    :" . DeviceTAC::redirect()->location . "&nbsp;*&nbsp;" . DeviceTAC::redirect()->path . "&nbsp;*&nbsp;" . DeviceTAC::redirect()->query . "&nbsp;*&nbsp;" . DeviceTAC::redirect()->param . "</p>";
                                                            echo "<p>Request Methode:" . $_SERVER["REQUEST_METHOD"] . "</p>";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div id="zsld-support-tab-content-user">
                                                </div>
                                            </div>
                                        </div>

                                        <script type="text/javascript" src="lib/bootstrap-treeview/js/bootstrap-treeview.js"></script>
                                        <script type="text/javascript">
                                        
                                            var FORM_ID = 1

                                            var URL_FORMS = "https://zso-aargausued.ch/map/api/read?type=forms&object=formulare&id=0";
                                            $http_forms = new Rest(
                                                function(response) {
                                                    console.info('Forms ', response);

                                                    var formsRec = [{}];

                                                    for(var r = 1; r < response.length; r++) {
                                                        formsRec.push({});
                                                        for(var f = 0; f < response[0].length; f++) {
                                                            formsRec[0][f] = response[0][f];
                                                            formsRec[r][formsRec[0][f]] = response[r][f];
                                                        }
                                                    }

                                                    console.info('Forms object ', formsRec);

                                                    //URL_FORM = "https://zso-aargausued.ch/map/api/read?type=forms&object=formulare&id=1";
                                                    var URL_FORM = "https://zso-aargausued.ch/map/api/read?type=forms&object=formulare&id=" + formsRec[FORM_ID]["Form"];
                                                    //URL_DATA = "https://zso-aargausued.ch/map/api/read?type=objectstore&object=users&id=-1";
                                                    var URL_DATA = formsRec[FORM_ID]["DataBinding"];

                                                    console.info('Form URL ', URL_FORM);
                                                    console.info('Data URL ', URL_DATA);
                                                    
                                                    $http_form = new Rest(
                                                        function(response) {
                                                            console.info('Form definition ', response);
                                                            //passed('Users');

                                                            var definition = response;
                                                            
                                                            $http_data = new Rest(
                                                                function(response) {
                                                                    console.info('Data ', response);

                                                                    var transformedData;

                                                                    $form_save = new Rest(
                                                                        function(response) {
                                                                            console.info('From data saved ', response);
                                                                        },
                                                                        function(response) {
                                                                            console.error('Error attempting to save the formdata ', response);
                                                                        }
                                                                    );
                                                                    function saveForm(formdata) {
                                                                        $form_save.post(formsRec[FORM_ID]["DataStorage"], JSON.stringify( formdata || {} ));
                                                                    }

                                                                    if (formsRec[FORM_ID]["Navigation"]) {
                                                                        if (formsRec[FORM_ID]["DataTransformator"] != "") {
                                                                            var dataTransformator = new Function("request", "response", "form", "zsldDB", formsRec[FORM_ID]["DataTransformator"] + "\nreturn [request, response];");
                                                                            transformedData = dataTransformator(response, undefined, definition, saveForm)[1];
                                                                        } else {
                                                                            transformedData = response;
                                                                        }

                                                                        $(function() {
                                                                            var $formNavigationTree = initNavigationTree("users", "Suchen", "Search …", '#zsld-support-tab-content-user', transformedData, formsRec[FORM_ID]["DataTransformator"], formsRec[FORM_ID]["DataStorage"]);
                                                                        });
                                                                    } else {

                                                                        $(function() {
                                                                            var $formView = $('#zsld-support-tab-content-user').html(formLayout(definition, response.objects[0], formsRec[FORM_ID]["DataTransformator"], formsRec[FORM_ID]["DataStorage"]));
                                                                        });
                                                                    }
                                                                },
                                                                function(response) {
                                                                    console.error('Error attempting to get the Data ', response);
                                                                }
                                                            );
                                                            $http_data.get(URL_DATA, JSON.stringify({}));
                                                        },
                                                        function(response) {
                                                            console.error('Error attempting to get the Form definition ', response);
                                                        }
                                                    );
                                                    $http_form.get(URL_FORM, JSON.stringify({}));
                                                },
                                                function(response) {
                                                    console.error('Error attempting to get the Forms ', response);
                                                }
                                            );
                                            $http_forms.get(URL_FORMS, JSON.stringify({}));

                                            function transformByKey(data, key, defaultValue) {
                                                var value = data;
                                                var hasKey = key.match(/\$\{(.*)\}/);

                                                if (hasKey) {
                                                    var keys = hasKey[1].split('.');
                                                    var keyExists = true;
                                                    for(var k = 0; k < keys.length; k++) {
                                                        if (keyExists && value[keys[k]]) {
                                                            value = value[keys[k]];
                                                        } else {
                                                            value = data;
                                                            keyExists = false;
                                                        }
                                                    }
                                                } else {
                                                    keyExists = false;
                                                }

                                                if (!keyExists) {
                                                    value = defaultValue;
                                                }

                                                return value;
                                            }

                                            function formViewLayout(definition, data, dataTransformatorString, dataStorageURL) {
                                                var group = '';
                                                var style = '';
                                                    style += '       <style type="text/css">';
                                                    for(var s = 10; s < 100; s += 10) {
                                                        style += '           .input-group>.input-group-prepend.zsld-fixed-width-' + s + ' {';
                                                        style += '               flex: 0 0 ' + s + '%;';
                                                        style += '           }';
                                                        group += '           .input-group .input-group-text.zsld-fixed-width-' + s + ( s < 90 ? ',' : '' );
                                                    }
                                                    style += group;
                                                    style += ' {';
                                                    style += '               width: 100%;';
                                                    style += '           }';
                                                    style += '       </style>';
                                                var formOpen = '       <form>\n';
                                                var formBody = '';
                                                var formClose = '           <button id="zsld-form-save-button" class="btn btn-success" type="button">Speichern</button>\n';
                                                    formClose += '           <script type="text/javascript">\n';
                                                    formClose += '                  var transformedData;\n';
                                                    formClose += "                  var dataTransformator = new Function('request', 'response', 'form', 'zsldDB', `\n" + dataTransformatorString + "\nreturn [request, response];\n`);\n";
                                                    formClose += '                  function transformator(response, form) { transformedData = dataTransformator(undefined, response, form, Rest)[0]; console.log(transformedData); }\n';
                                                    formClose += '                  $("#zsld-form-save-button").on("click", { save: transformator }, function ( event ) { event.data.save($($(this).parent()).serializeArray(), $($(this).parent())) });\n';
                                                    formClose += '           <\/script>\n';
                                                    formClose += '       </form>\n';
                                                
                                                var formComponents = '';

                                                for(var i = 1; i < definition.length; i++) {

                                                    //formComponents += '    <div class="form-row">\n';
                                                    //formComponents += '        <div class="col-sm-8 mb-3">\n';
                                                    formComponents += '            <div class="form-group">\n';
                                                    formComponents += '                <div id="zsld-form-group-' + definition[i][0] + '" class="input-group">\n'; //style="margin-bottom: 15px; border-radius: 4px;"
                                                    formComponents += '                    <div id="zsld-form-group-' + definition[i][0] + '-prepend" class="input-group-prepend ' + (definition[i][11] ? 'zsld-fixed-width-' + definition[i][11] : '') + '">\n';
                                                    formComponents += '                        <span id="zsld-form-icon-' + definition[i][0] + '" class="input-group-text"><i class="' + definition[i][1] + ' fa-fw"></i></span>\n';
                                                    if (definition[i][2] != "") {
                                                        formComponents += '                        <span id="zsld-form-label-' + definition[i][0] + '" class="input-group-text ' + (definition[i][11] ? 'zsld-fixed-width-' + definition[i][11] : '') + '">' + definition[i][2] + '</span>\n';
                                                    }
                                                    formComponents += '                    </div>\n';
                                                    for(var e = 0; e < definition[i][0].split('_').length; e++) {
                                                        switch (definition[i][4].split('_')[e]) {
                                                            case "xyz":
                                                                break;
                                                            case "radio":
                                                                formComponents += '                    <div class="form-control">\n';
                                                                for(var o = 0; o < definition[i][5].split('_')[e].split(',').length; o++) {
                                                                    formComponents += '                        <div class="form-check form-check-inline">\n';
                                                                    formComponents += '                            <input id="zsld-form-input-' + definition[i][0].split('_')[e] + '-' + (definition[i][5].split('_')[e].split(',')[o]).toLowerCase() + '" name="' + definition[i][0].split('_')[e] + '" class="form-check-input" value="' + transformByKey(data, definition[i][3].split('_')[e].split(',')[o], definition[i][5].split('_')[e].split(',')[o]) + '" type="' + definition[i][4].split('_')[e] + '" ' + ( definition[i][6] == "disabled" || definition[i][6] == "readonly"  ? ( definition[i][6] == "disabled"  ? 'disabled' : 'readonly' ) : '' ) + '>\n';
                                                                    formComponents += '                            <label class="form-check-label" for="zsld-form-input-' + definition[i][0].split('_')[e] + '">' + definition[i][5].split('_')[e].split(',')[o] + '</label>\n';
                                                                    formComponents += '                        </div>\n';
                                                                }
                                                                formComponents += '                    </div>\n';
                                                                break;
                                                            case "text":
                                                            case "number":
                                                                formComponents += '                    <input id="zsld-form-input-' + definition[i][0].split('_')[e] + '" name="' + definition[i][0].split('_')[e] + '" class="form-control"' + (definition[i][5].split('_')[e] != "" ? ' value="' + transformByKey(data, definition[i][3].split('_')[e], definition[i][5].split('_')[e]) + '"' : '') + ' type="' + definition[i][4].split('_')[e] + '" placeholder="' + definition[i][5].split('_')[e] + '" ' + ( definition[i][6] == "disabled" || definition[i][6] == "readonly"  ? ( definition[i][6] == "disabled"  ? 'disabled' : 'readonly' ) : '' ) + '>\n';
                                                                break;
                                                            default:
                                                                break;
                                                        }
                                                    }
                                                    if (definition[i][7] != "") {
                                                        formComponents += '                    <div id="zsld-form-group-' + definition[i][0] + '-append" class="input-group-append">\n'; //style="font-size: 14px;"
                                                        formComponents += '                        <button id="zsld-form-action-' + definition[i][0] + '-' + definition[i][7] + '" class="btn ' + definition[i][8] + '" type="button" ' + ( definition[i][6] == "disabled" ? "disabled" : "" ) + '>' + definition[i][9] + '</button>\n';
                                                        formComponents += '                    </div>\n';
                                                        formComponents += '                    <script type="text/javascript">\n';
                                                        formComponents += '                        ' + definition[i][10] + '\n';
                                                        formComponents += '                    <\/script>\n';
                                                    }
                                                    formComponents += '                </div>\n';
                                                    formComponents += '            </div>\n';
                                                    //formComponents += '        </div>\n';
                                                    //formComponents += '    </div>\n';

                                                }

                                                formBody = formComponents;

                                                return style + formOpen + formBody + formClose;
                                            }

                                            function formNavigation(name, text, placeholder) {
                                                var formNavigation = '';

                                                formNavigation += '<div id="zsld-navigation-' + name + '" class="zsld-navigation-container-fluid">';
                                                formNavigation += '    <div class="row">';
                                                formNavigation += '        <div class="col-sm-8">';
                                                formNavigation += '        </div>';
                                                formNavigation += '        <div class="col-sm-4">';
                                                formNavigation += '            <div id="zsld-navigation-group-' + name + '" class="input-group" style="margin-bottom: 15px; border-radius: 4px;">';
                                                formNavigation += '                <input id="zsld-navigation-input-' + name + '" type="text" class="form-control" placeholder="' + placeholder + '">';
                                                formNavigation += '                <span id="zsld-navigation-group-' + name + '-append" class="input-group-append" style="font-size: 14px;">';
                                                formNavigation += '                    <button id="zsld-navigation-action-' + name + '-search" class="btn btn-success zsld-navigation-action-' + name + '-node" type="button">' + text + '</button>';
                                                formNavigation += '                </span>';
                                                formNavigation += '            </div>';
                                                formNavigation += '        </div>';
                                                formNavigation += '    </div>';
                                                formNavigation += '    <div class="row">';
                                                formNavigation += '        <div class="col-sm-2">';
                                                formNavigation += '            <div id="zsld-navigation-treeview-' + name + '" class=""></div>';
                                                formNavigation += '        </div>';

                                                formNavigation += '        <div class="col-sm-10">';
                                                formNavigation += '            <div id="zsld-navigation-content-' + name + '" class="zsld-navigation-content-container-fluid"></div>';
                                                formNavigation += '        </div>';

                                                formNavigation += '    </div>';
                                                formNavigation += '</div>';

                                                return formNavigation;
                                            }

                                            function initNavigationTree(name, text, placeholder, parent, treeData, dataTransformatorString, dataStorageURL) {
                                                $(parent).html(formNavigation(name, text, placeholder));

                                                var treeView = $('#zsld-navigation-treeview-' + name).treeview({
                                                    data: treeData,
                                                    levels: 1,
                                                    onNodeSelected: function(event, node) {
                                                        $('#zsld-navigation-content-' + name).html(formViewLayout(node.form, node.data, dataTransformatorString, dataStorageURL));
                                                    },
                                                    onNodeUnselected: function (event, node) {
                                                        $('#zsld-navigation-content-' + name).html('');
                                                    },
                                                    onNodeCollapsed: function(event, node) {
                                                        //$('#zsld-navigation-content-' + name).html('<p>' + node.text + ' was collapsed</p>');
                                                    },
                                                    onNodeExpanded: function (event, node) {
                                                        //$('#zsld-navigation-content-' + name).html('<p>' + node.text + ' was expanded</p>');
                                                    },
                                                    showBorder: false,
                                                    nodeIcon: "fas fa-user-edit",
                                                    expandIcon: "fas fa-angle-double-down",
                                                    collapseIcon: "fas fa-angle-double-up",
                                                    selectedColor: '#0039ef',
                                                    searchResultColor: '#0039ef',
                                                    selectedBackColor: '#ff5208'
                                                });

                                                var findNodes = function() {
                                                    return treeView.treeview('search', [ $('#zsld-navigation-input-' + name).val(), { ignoreCase: false, exactMatch: false, revealResults: false } ]);
                                                };
                                                var treeNodes = findNodes();

                                                // Select/unselect/toggle nodes
                                                $('#zsld-navigation-input-' + name).on('keyup', function (e) {
                                                    treeNodes = findNodes();
                                                    $('.zsld-navigation-action-' + name + '-node').prop('disabled', !(treeNodes.length >= 1));
                                                });

                                                $('#zsld-navigation-action-' + name + '-search.zsld-navigation-action-' + name + '-node').on('click', function (e) {
                                                    treeView.treeview('selectNode', [ treeNodes, { silent: false }]);
                                                });

                                                return treeView;

                                            };

                                        </script>

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
?>