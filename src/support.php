<?php
$GLOBALS["SUPPORT"] = [];

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");


require_once 'config/setup.php';

$setup = new Setup();

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
    </head>

    <body>
        <script src="https://api3.geo.admin.ch/loader.js?lang=de&version=4.4.2"></script>
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

        <!-- Warper -->
        <div id="wrapper">
            <div style="margin: 5px;" class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Support: Geräteangaben</h3>
                </div>
                <div class="panel-body">
                
                    <?php 
//echo $_SERVER["REQUEST_METHOD"] . "<br />\n";
if (defined("DEVICE_TAC")) {
    echo "<pre>Dein DEVICE_TAC ist: " . constant("DEVICE_TAC") . "</pre><br />\n";
    echo "<pre>" . str_replace(array("<", ">"), array("\<", "\>"), json_encode($GLOBALS["SUPPORT"] , JSON_PRETTY_PRINT)). "</pre><br />\n"; 
    $keys = '';
    if ( $keys = json_decode( file_get_contents( DATA_PATH  . 'registration-popup-key.txt' ) ) ) {
        if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
            if ( isset( $_POST['enabledialog']) && $_POST['enabledialog'] != "" ) {
                $keys[] = constant("DEVICE_TAC");
                echo "<b>Das Gerät wurde zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
            } else {
                if ( in_array( DEVICE_TAC, $keys ) ) {
                    foreach (array_keys($keys, constant("DEVICE_TAC")) as $key) {
                        unset($keys[$key]);
                    }
                }
                echo "<b>Das Gerät ist nicht länger zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
            }
            if (!file_put_contents(DATA_PATH  . 'registration-popup-key.txt', json_encode($keys, JSON_PRETTY_PRINT))) {
                echo "<b>Fehler: Die Einstellungen konnten nicht gespeichert werden!</b><br />\n";
                $keys = '';
            }
        } else {
            ?>
            <form id="zsld-support-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
                <style>
                /* The switch - the box around the slider */
                .switch {
                    position: relative;
                    display: inline-block;
                    width: 60px;
                    height: 34px;
                    left: 10px;
                    bottom: 7px;
                    float: right;
                }

                /* The confirm buton - the button to submit */
                .confirm {
                    position: relative;
                    display: inline-block;
                    bottom: 7px;
                    float: right;
                }
        
                /* Hide default HTML checkbox */
                .switch input {
                    display:none;
                }
        
                /* The slider */
                .slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    -webkit-transition: .4s;
                    transition: .4s;
                }
        
                .slider:before {
                    position: absolute;
                    content: "";
                    height: 26px;
                    width: 26px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    -webkit-transition: .4s;
                    transition: .4s;
                }
        
                input.default:checked + .slider {
                    background-color: #444;
                }
                input.primary:checked + .slider {
                    background-color: #2196F3;
                }
                input.success:checked + .slider {
                    background-color: #8bc34a;
                }
                input.info:checked + .slider {
                    background-color: #3de0f5;
                }
                input.warning:checked + .slider {
                    background-color: #FFC107;
                }
                input.danger:checked + .slider {
                    background-color: #f44336;
                }
        
                input:focus + .slider {
                    box-shadow: 0 0 1px #2196F3;
                }
        
                input:checked + .slider:before {
                    -webkit-transform: translateX(26px);
                    -ms-transform: translateX(26px);
                    transform: translateX(26px);
                }
        
                /* Rounded sliders */
                .slider.round {
                    border-radius: 34px;
                }
        
                .slider.round:before {
                    border-radius: 50%;
                }
                </style>

                <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading">
                    <?php
                        if ( in_array( DEVICE_TAC, $keys ) ) {
                            echo "<b>Das Gerät ist zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
                            $register = FALSE;
                        } else {
                            echo "<b>Das Gerät ist nicht zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
                            $register = TRUE;
                        }
                        
                        // //echo '        <label for="zsld-support-checkbox-enabledialog">Registrationsdialog immer anzeigen</label>' . "\n";
                        // //echo '        <input id="zsld-support-checkbox-enabledialog" name="enabledialog" type="checkbox" class=""' . ( $register ?  " checked" : "" ) . "></input>\n";
                        // echo '        <div class="input-group">' . "\n";
                        // echo '            <label for="zsld-support-checkbox-enabledialog">Registrationsdialog immer anzeigen</label>' . "\n";
                        // echo '            <div class="input-group-prepend">' . "\n";
                        // echo '                  <div class="input-group-text">' . "\n";
                        // echo '                      <input id="zsld-support-checkbox-enabledialog" name="enabledialog" type="checkbox" aria-label="Checkbox enabledialog"></input>' . "\n";
                        // echo '                  </div>' . "\n";
                        // echo '            </div>' . "\n";
                        // echo '            <button id="zsld-support-button-submit" class="btn btn-success" type="submit">Speichern</button>' . "\n";
                        // echo '        </div>' . "\n";
                    ?>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                Registrationsdialog immer anzeigen
                                <label for="zsld-support-checkbox-enabledialog" class="switch ">
                                    <input id="zsld-support-checkbox-enabledialog" name="enabledialog" aria-label="Checkbox enabledialog" type="checkbox" class="primary" <?php echo ( $register ?  " checked" : "" ) ?>>
                                    <span class="slider round"></span>
                                </label>
                            </li>
                        </ul>
                        <button id="zsld-support-button-submit" class="btn btn-success confirm" type="submit">Speichern</button>
                    </div>
                </div> 

            </form>
            <?php
        }
    }
} else {
    echo "<pre>Fehler: kein DEVICE_TAC definiert.</pre><br />\n";
}
                    ?>
                </div>
            </div>
        </div>
        <!-- /#wrapper -->

    </body>

</html>