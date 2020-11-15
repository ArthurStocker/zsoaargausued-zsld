<?php
$GLOBALS["SUPPORT"] = [];

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");


require_once 'config/setup.php';

$setup = new Setup();


if ( DeviceTAC::read( 'person' ) && json_decode( DeviceTAC::read( 'person' ) )->display === "unbekannt" ) {
    $expiration = "-10 seconds";
    DeviceTAC::abort();
    DeviceTAC::restore( $expiration );
    DeviceTAC::write( 'expiration', $expiration );
    DeviceTAC::commit();
}

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

        <!-- Warper -->
        <div id="wrapper">
            <div style="margin: 5px;" class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Fahrzeugangaben</h3>

                    <div class="btn-toolbar modal-confirm" role="toolbar" aria-label="Toolbar with button groups">
                        <div class="btn-group btn-group-xs mr-2" role="group" aria-label="Default Buttongroup">
                            <button id="zsld-odometer-button-submit" class="btn btn-success" type="submit">Speichern</button>
                        </div>
                    </div>
                </div>
                <div class="panel-body" style="height: calc(100vh - 58px);">
                <?php 
                if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
                    if ( ( isset( $_POST['odometercount']) && $_POST['odometercount'] != "" ) && ( isset( $_POST['carnr']) && $_POST['carnr'] != "" ) ) {
                        /**
                         * Daten speichern und redirect zur MAP
                         * ex: https://www.zso-aargausued.ch/map/odometer?tic=447E317E0E9199AE86061C4EB996C5E2&data=park&type=yourcar&id=11&valid=2020-04-26T12:30:00%2b02:00&errno=&error=
                         */
                        require_once 'class/TransactionStore.php';
                        foreach ( array_keys($_POST) as $key ) {
                            $data[$key] = $_POST[$key];
                        }
                        $result = TransactionStore::save($data["odometercount"], "odometer", $data['carnr'], TRUE, DATA_PATH . 'odometer.json');
                        $data["errno"] = $result["errno"];
                        $data["error"] = $result["error"];
                        header("refresh:20; url=https://" . $_SERVER['HTTP_HOST'] . "/map/?tic=" . $data['tic'] . "&data=" . $data['data'] . "&type=" . $data['type'] . "&id=" . $data['carnr'] . "&valid=" . $data['valid'] . "&errno=" . $data['errno'] . "&error=" . $data['error']); 
                        //header("Location: https://" . $_SERVER['HTTP_HOST'] . "/map/?tic=" . $data['tic'] . "&data=" . $data['data'] . "&type=" . $data['type'] . "&id=" . $data['carnr'] . "&valid=" . $data['valid'] . "&errno=" . $data['errno'] . "&error=" . $data['error'], true, 303);
                        ?>
                        KM-Stand <b><?php echo $data['odometercount'] ?></b> für Fahrzeug Nr. <b><?php echo $data['carnr'] ?></b> gespeichert.</b><br />
                        <?php //echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>" ?>
                        <?php //echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>" ?>
                        <?php
                    } else {
                        header("Location: https://" . $_SERVER['HTTP_HOST'] . "/map/odometer?tic=" . $_POST['tic'] . "&data=" . $_POST['data'] . "&type=" . $_POST['type'] . "&id=" . $_POST['carnr'] . "&valid=" . $_POST['valid'] . "&odometercount=" . $_POST['odometercount'] . "&errno=1" . "&error=missing_data", true, 303);
                    }
                } else {
                    if ( isset( $_GET['errno'] ) && $_GET['errno'] === "1") {
                        echo "<pre>Fehler: es wurden nicht alle Felder ausgefüllt.</pre>\n";
                    }
                    ?>
                    <form id="zsld-odometer-form" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">

                        <!-- hidden fields nedded for redirect -->
                        <span style="display: none;">
                            <div class="form-group row">
                                <label for="zsld-odometer-number-id" class="col-sm-2 col-form-label">ID</label>
                                <div class="col-sm-10">
                                    <input id="zsld-odometer-number-id" name="id" aria-label="Number id" type="number" class="form-control" value="<?php echo ( isset($_GET['id']) ? $_GET['id'] : "" ) ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="zsld-odometer-text-tic" class="col-sm-2 col-form-label">TIC</label>
                                <div class="col-sm-10">
                                    <input id="zsld-odometer-text-tic" name="tic" aria-label="Text tic" type="text" class="form-control" value="<?php echo ( isset($_GET['tic']) ? $_GET['tic'] : "" ) ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="zsld-odometer-text-data" class="col-sm-2 col-form-label">Data</label>
                                <div class="col-sm-10">
                                    <input id="zsld-odometer-text-data" name="data" aria-label="Text data" type="text" class="form-control" value="<?php echo ( isset($_GET['data']) ? $_GET['data'] : "" ) ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="zsld-odometer-text-type" class="col-sm-2 col-form-label">Type</label>
                                <div class="col-sm-10">
                                    <input id="zsld-odometer-text-type" name="type" aria-label="Text type" type="text" class="form-control" value="<?php echo ( isset($_GET['type']) ? $_GET['type'] : "" ) ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="zsld-odometer-text-valid" class="col-sm-2 col-form-label">Valid</label>
                                <div class="col-sm-10">
                                    <input id="zsld-odometer-text-valid" name="valid" aria-label="Text valid" type="text" class="form-control" value="<?php echo ( isset($_GET['valid']) ? $_GET['valid'] : "" ) ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="zsld-odometer-number-errno" class="col-sm-2 col-form-label">ErroNo</label>
                                <div class="col-sm-10">
                                    <input id="zsld-odometer-number-errno" name="errno" aria-label="Number errno" type="number" class="form-control" value="<?php echo ( isset($_GET['errno']) ? $_GET['errno'] : 0 ) ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="zsld-odometer-text-error" class="col-sm-2 col-form-label">Error</label>
                                <div class="col-sm-10">
                                    <input id="zsld-odometer-text-error" name="error" aria-label="Text error" type="text" class="form-control" value="<?php echo ( isset($_GET['error']) ? $_GET['error'] : "" ) ?>">
                                </div>
                            </div>
                        </span>

                        <div class="form-group row">
                            <label for="zsld-odometer-number-carnr" class="col-sm-2 col-form-label">Nr.</label>
                            <div class="col-sm-10">
                                <input id="zsld-odometer-number-carnr" name="carnr" aria-label="Number carnr" type="number" class="form-control" value="<?php echo ( isset($_GET['id']) ? $_GET['id'] : "" ) ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="zsld-odometer-number-count" class="col-sm-2 col-form-label">KM-Stand</label>
                            <div class="col-sm-10">
                                <input id="zsld-odometer-number-count" name="odometercount" aria-label="Number odometercount" type="number" class="form-control" value="<?php echo ( isset($_GET['odometercount']) ? $_GET['odometercount'] : "" ) ?>">
                            </div>
                        </div>
                    </form>
                    <?php
                }
                ?>
                </div>
            </div>
        </div>
        <!-- /#wrapper -->

    </body>

</html>
<?php
}
?>