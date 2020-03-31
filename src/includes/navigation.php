<!-- partial:navigationgrid -->
<span class="panel-item-nav-left">
    <?php 
        $files = array();

        foreach (new DirectoryIterator(ITEM_PATH) as $file) {
            if ($file->isDot()) continue;
            if ($file->getExtension() == 'php') {
                $files[] = $file->getFilename();
            }
        }

        sort($files);

        foreach ($files as $file) {
            include(ITEM_PATH . "/" . $file);
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
        $files = array();

        foreach (new DirectoryIterator(ADMIN_PATH) as $file) {
            if ($file->isDot()) continue;
            if ($file->getExtension() == 'php') {
                $files[] = $file->getFilename();
            }
        }

        sort($files);

        foreach ($files as $file) {
            include(ADMIN_PATH . "/" . $file);
        }
    ?>
</span>