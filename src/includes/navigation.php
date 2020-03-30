<!-- partial:navigationgrid -->
<span class="panel-item-nav-left">
    <?php 
        $path  = './includes/nav/items';
        $files = array();

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) continue;
            if ($file->getExtension() == 'php') {
                $files[] = $file->getFilename();
            }
        }

        sort($files);

        foreach ($files as $file) {
            include($path . "/" . $file);
        }
    ?>
</span>

<span class="panel-item-nav-center">
    <span class="panel-item-nav-center-info">
        <div class="alert alert-danger">Error</div>
        <div class="alert alert-success">Success</div>
    </span>
</span>

<span class="panel-item-nav-right">
    <?php
        $path  = './includes/nav/admin';
        $files = array();

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) continue;
            if ($file->getExtension() == 'php') {
                $files[] = $file->getFilename();
            }
        }

        sort($files);

        foreach ($files as $file) {
            include($path . "/" . $file);
        }
    ?>
</span>