<?php
require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET");


require_once 'config/setup.php';
require_once 'config/ui.php';

$setup = new Setup();
$ui = new UI();


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
        <?php 
            $files = array();

            foreach (new DirectoryIterator(COMPONENT_PATH) as $file) {
                if ($file->isDot()) continue;
                if ($file->getExtension() == 'css') {
                    $files[] = $file->getFilename();
                }
            }

            sort($files);

            foreach ($files as $file) {
                echo '<link rel="stylesheet" href="' . COMPONENT_PATH . '/' . $file . '?version=' . time() . '">';
            }
        ?>
        <?php 
            $files = array();

            foreach (new DirectoryIterator(PLUGIN_PATH) as $file) {
                if ($file->isDot()) continue;
                if ($file->getExtension() == 'css') {
                    $files[] = $file->getFilename();
                }
            }

            sort($files);

            foreach ($files as $file) {
                echo '<link rel="stylesheet" href="' . PLUGIN_PATH . '/' . $file . '?version=' . time() . '">';
            }
        ?>
        <title>ZSO aargauSüd - Lagedarstellung</title>
        <?php $setup->worker();?>
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
        <?php $ui->init();?>

        <!-- Warper -->
        <div id="wrapper">

            <?php 
                $files = array();

                foreach (new DirectoryIterator(COMPONENT_PATH) as $file) {
                    if ($file->isDot()) continue;
                    if ($file->getExtension() == 'php') {
                        $files[] = $file->getFilename();
                    }
                }

                sort($files);

                foreach ($files as $file) {
                    include(COMPONENT_PATH . "/" . $file);
                }
            ?>
            <?php 
                $files = array();

                foreach (new DirectoryIterator(COMPONENT_PATH) as $file) {
                    if ($file->isDot()) continue;
                    if ($file->getExtension() == 'js') {
                        $files[] = $file->getFilename();
                    }
                }

                sort($files);

                foreach ($files as $file) {
                    echo '<script src="' . COMPONENT_PATH . '/' . $file . '?version=' . time() . '"></script>';
                }
            ?>
            <!-- Plugins -->
            <?php 
                $files = array();

                foreach (new DirectoryIterator(PLUGIN_PATH) as $file) {
                    if ($file->isDot()) continue;
                    if ($file->getExtension() == 'php') {
                        $files[] = $file->getFilename();
                    }
                }

                sort($files);

                foreach ($files as $file) {
                    include(PLUGIN_PATH . "/" . $file);
                }
            ?>
            <?php 
                $files = array();

                foreach (new DirectoryIterator(PLUGIN_PATH) as $file) {
                    if ($file->isDot()) continue;
                    if ($file->getExtension() == 'js') {
                        $files[] = $file->getFilename();
                    }
                }

                sort($files);

                foreach ($files as $file) {
                    echo '<script src="' . PLUGIN_PATH . '/' . $file . '?version=' . time() . '"></script>';
                }
            ?>
        </div>
        <!-- /#wrapper -->

    </body>

</html>
<?php
}
?>