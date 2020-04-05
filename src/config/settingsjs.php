/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * Die Konfigurationswerte ist im config/settings.php
 */
// Service Worker
var SW_REGISTRATION;
if ('serviceWorker' in navigator && 'PushManager' in window) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('<?php echo SERVICEWORKER;?>')
    .then(registration => {
      console.log('Service Worker registered with scope:', registration.scope);
      SW_REGISTRATION = registration;
    })
    .catch(err => {
      console.error('Service Worker registration failed:', err);
    });
  });
} else {
  console.warn('Service Worker or Push Manager is not supported!');
}

<?php
if ($xlsx = SimpleXLSX::parse(__DIR__ . SETTINGS)) {
  // Produce array keys from the array values of 1st array element
  $fields = $rows = [];

  foreach ( $xlsx->rows(0) as $k => $r ) {
    if ( $k === 0 ) {
      $fields = $r;
      continue;
    }
    $values = array_combine( $fields, $r );
    echo "// " . $values['§Institution'] . "\n";
    echo "var MAP_CENTER_X = " . $values['KoordinateX'] . ";\n";
    echo "var MAP_CENTER_Y = " . $values['KoordinateY'] . ";\n\n";
  }

  foreach ( $xlsx->rows(1) as $k => $r ) {
    if ( $k === 0 ) {
        $fields = $r;
        continue;
    }
    $values = array_combine( $fields, $r );
    if ($values['active'] === 'true' && $values['hide'] === 'false' && ($values['system'] === 'all' || $values['system'] === SYSTEM) ) {
      echo "// " . $values['description'] . "\n";
      if ($values['type'] === 'string') {
        $string = "'";
      } else {
        $string = "";
      }
      echo "var " . $values['key'] . " = " . $string . $values['value'] . $string . ";\n\n";
    }
  }
} else {
  $data = SimpleXLSX::parseError();
}
?>
