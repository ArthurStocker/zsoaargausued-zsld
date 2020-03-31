<!-- partial:scripts -->
<script src='https://use.fontawesome.com/releases/v5.4.1/js/all.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.2/jquery.xdomainrequest.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/dist/typeahead.bundle.js'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js'></script>
<script>
<?php include('config/settingsjs.php');?>
</script>
<script src="/map/class/Rest.js"></script>
<script src="/map/includes/lib/helper.js"></script>
<script src="/map/includes/lib/map.js"></script>
<script src="/map/includes/lib/api.js"></script>
<?php 
    $files = array();

    foreach (new DirectoryIterator(ITEM_PATH) as $file) {
        if ($file->isDot()) continue;
        if ($file->getExtension() == 'js') {
            $files[] = $file->getFilename();
        }
    }

    sort($files);

    foreach ($files as $file) {
        echo '<script src="' . ITEM_PATH . '/' . $file . '"></script>';
    }
?>
<?php 
    $files = array();

    foreach (new DirectoryIterator(ADMIN_PATH) as $file) {
        if ($file->isDot()) continue;
        if ($file->getExtension() == 'js') {
            $files[] = $file->getFilename();
        }
    }

    sort($files);

    foreach ($files as $file) {
        echo '<script src="' . ADMIN_PATH . '/' . $file . '"></script>';
    }
?>
<script src="/map/script.js"></script>