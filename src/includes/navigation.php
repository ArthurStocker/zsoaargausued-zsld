<!-- partial:navigationgrid -->
<span class="panel-item-nav-left">
    <?php 
        $path  = './includes/nav/items';

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) continue;
            if ($file->getExtension() == 'php') {
                include($path . "/" . $file->getFilename());
            }
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

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) continue;
            if ($file->getExtension() == 'php') {
                include($path . "/" . $file->getFilename());
            }
        }
    ?>
</span>