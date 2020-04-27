<!-- Sidebar -->
<nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
    <ul class="nav sidebar-nav">
        <li class="sidebar-brand">
            <a href="http://www.zso-aargausued.ch">
                ZSO aargauSüd
            </a>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Über uns <span class="caret"></span></a>
            <ul class="dropdown-menu O1" role="menu">
                <li class="dropdown-header">Über uns</li>
                <li><a href="https://www.zso-aargausued.ch/?page_id=27">Kontakt</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Funktionen <span class="caret"></span></a>
            <ul class="dropdown-menu O2" role="menu">
                <li class="dropdown-header">Spezielle Funktionen</li>
                <?php 
                    $files = array();

                    foreach (new DirectoryIterator(SERVICES_PATH) as $file) {
                        if ($file->isDot()) continue;
                        if ($file->getExtension() == 'php') {
                            $files[] = $file->getFilename();
                        }
                    }

                    sort($files);

                    foreach ($files as $file) {
                        include(SERVICES_PATH . "/" . $file);
                    }
                ?>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Einstellungen <span class="caret"></span></a>
            <ul class="dropdown-menu O3" role="menu">
                <li class="dropdown-header">Einstellungen</li>
                <?php 
                    $files = array();

                    foreach (new DirectoryIterator(SETTINGS_PATH) as $file) {
                        if ($file->isDot()) continue;
                        if ($file->getExtension() == 'php') {
                            $files[] = $file->getFilename();
                        }
                    }

                    sort($files);

                    foreach ($files as $file) {
                        include(SETTINGS_PATH . "/" . $file);
                    }
                ?>
            </ul>
        </li>
    </ul>
</nav>
<!-- /#sidebar-wrapper -->