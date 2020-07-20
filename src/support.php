<?php
$GLOBALS["SUPPORT"] = [];

require_once 'class/DeviceTAC.php';

DeviceTAC::build(TRUE, "GET, POST");


require_once 'config/setup.php';

$setup = new Setup();


//echo $_SERVER["REQUEST_METHOD"] . "<br />\n";
if (defined("DEVICE_TAC")) {
    echo "<pre>Dein DEVICE_TAC ist: " . constant("DEVICE_TAC") . "</pre><br />\n";
    echo "<pre>" . json_encode($GLOBALS["SUPPORT"] , JSON_PRETTY_PRINT). "</pre><br />\n"; 
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
            if ( in_array( DEVICE_TAC, $keys ) ) {
                echo "<b>Das Gerät ist zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
            } else {
                echo "<b>Das Gerät ist nicht zur Anzeige des Registrationsdialoges vorgemerkt.</b><br />\n";
            }
            echo '<form id="zsld-support-form" action="' .  $_SERVER['SCRIPT_NAME'] . '" method="POST"' . ">\n";
            echo '    <label for="zsld-support-checkbox-enabledialog">Registrationsdialog immer anzeigen</label>' . "\n";
            echo '        <input id="zsld-support-checkbox-enabledialog" name="enabledialog" type="checkbox" class=""' . ( in_array( DEVICE_TAC, $keys ) ?  "" :  "checked" ) . ">\n";
            echo '        <button id="zsld-support-button-submit" class="" type="submit">Speichern</button>' . "\n";
            echo '    </form>' . "\n";
        }
    }
} else {
    echo "<pre>Fehler: kein DEVICE_TAC definiert.</pre><br />\n";
}
?>