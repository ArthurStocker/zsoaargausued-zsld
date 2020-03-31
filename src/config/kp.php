<?php
define("ICON_APPLE", "/map/apple-touch-icon.png");
define("ICON_32", "/map/favicon-32x32.png");
define("ICON_16", "/map/favicon-16x16.png");
define("MANIFEST", "/map/site.webmanifest");

define("SERVICEWORKER", "zsld-service-worker.js");

define("DATA_PATH","../../../Public/");

define("ITEM_PATH","./includes/nav/items");
define("ADMIN_PATH","./includes/nav/admin");

define("URL_GA_SEARCH","/rest/services/api/SearchServer?sr=2056&searchText=%QUERY&type=locations");

define("URL_ZSLD_SETTINGS","//kp.zso-aargausued.ch:8443/map/api/read.php?type=system&object=settings&id=4");

define("URL_GEOJSON_STYLE","//kp.zso-aargausued.ch:8443/map/api/read.php?type=style&object=default&id=0");
define("URL_GEOJSON_FEATURE","//kp.zso-aargausued.ch:8443/map/api/read.php?type=feature&object=lage&id=0");
define("URL_GEOJSON_FEATURE_YAH","//kp.zso-aargausued.ch:8443/map/api/read.php?type=feature&object=settings&id=0");

define("FADING_DURATION", "500");
define("DISPLAY_DURATION", "5000");
?>