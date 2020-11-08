<?php
require_once 'config/settings.php';


require_once 'class/ObjectStore.php';


class DeviceTAC {

    private $debug;

    // array holding allowed Origin domains
    private $allowedOrigins = array(
        '(http(s)://)?(www\.)?zso-aargausued\.ch' //,
        /*
        '(http(s)://)?(www\.)?codepen\.io',
        '(http(s)://)?(www\.)?cdpn\.io',
        */
    );
    function _construct($debug = false) {
        $this->debug = $debug;
    }
    private function _htac() { 
        // Create formatted ID
        $htac       = '';

        for ($i = 1; $i <= 3; $i++) { 
            // Create a token
            switch($i) {
                case '1':
                    $token      = $_SERVER['SERVER_ADDR'];
                    $token     .= $_SERVER['REQUEST_URI'];
                    $token     .= uniqid(rand(), true);
                    break;
                case '2':
                    $token      = $_SERVER['REMOTE_ADDR'];
                    $token     .= $_SERVER['REQUEST_TIME'];
                    $token     .= uniqid(rand(), true);
                    break;
                case '3':
                    $token      = $_SERVER['HTTP_HOST'];
                    $token     .= $_SERVER['REQUEST_TIME'];
                    $token     .= uniqid(rand(), true);
                    break;
            }
            
            // ID is 128-bit hex
            $hash       = md5($token);
            
            // Compile very long ID 
            $htac      .= substr($hash,  0,  8) . 
                            substr($hash,  8,  4) .
                            substr($hash, 12,  4) .
                            substr($hash, 16,  4) .
                            substr($hash, 20, 12);
        }

        return $htac;
    }
    private function registerDevice() {

        $data = new stdClass();
        $data->id = "U" . dechex( date_timestamp_get( date_create() ));
        $data->display = "unbekannt";
        $data->properties = new stdClass();
        $data->properties->Name = "unbekannt";
        $data->properties->Vorname = "unbekannt";
        $data->properties->IPN = "unbekannt";
        $data->properties->trackme = false;
        $data->properties->notifymc = false;
        $data->forceconcurrentobjects = false;
        $data->concurrentobjectsallowed = false;

        //New device. Data access granted for 180 sec
        self::debug("ZSLDDEBUG_DEVICETAC_BUILD_1", json_decode( '{ "error": "Das Gerät wurde in der Geräte-Datenbank nicht gefunden. Neues Gerät, der Zugriff wird für 180 Sekunden gewährt damit eine Registrierung stattfinden kann." }' ) );
        self::session();
        self::write( 'auth', false );
        self::commit();

        $response = ObjectStore::save( json_encode( $data, JSON_PRETTY_PRINT ), "device", DEVICE_TAC, false, DATA_PATH . DATASTORE_DEVICE . '.json' );

        return $response;
    }
    public function headers($methods) {
        
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
            foreach ($this->allowedOrigins as $allowedOrigin) {
                if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
                    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                    header('Access-Control-Allow-Methods: ' . $methods);
                    header('Access-Control-Max-Age: 1000');
                    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
                    header('Access-Control-Allow-Credentials: true');
                    break;
                }
            }
        }

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: pre-check=0, post-check=0", false);
        header("Pragma: no-cache");

    }
    public static function debug($key, $message) {
        if (isset($GLOBALS["SUPPORT"])) {
            $GLOBALS["SUPPORT"][$key] = $message;
        }
    }
    public static function session( $lifetime = "+180 seconds" ) {
        if ($lifetime !== "" ) { 
            session_set_cookie_params ( $lifetime, "/map/", ".zso-aargausued.ch", TRUE, FALSE );
        }
        session_save_path( DATA_PATH );
        session_name( "ZSLDSESSION" );
        session_start();
        self::write( 'device', DEVICE_TAC );
        if ($lifetime !== "" ) {
            self::write( 'valid', date(DATE_ATOM, strtotime( $lifetime , time() ) ) );
        }
    }
    public static function restore( $lifetime = "+3600 seconds" ) { 
        if ( session_status() === 1 ) {
            self::debug("ZSLDDEBUG_ZSLDSESSION_RESTORE_1", json_decode( '{"STATUS": "1"}') );
            self::session();
        }

        if ( session_status() === 2 ) {
            // Session ID
            $sid = session_id();

            // Session schliessen
            self::commit();

            // Falls die Session aktive war, löschen wir auch das Session-Cookie.
            // Achtung: Damit wird die Session wieder aufehmen können müssen wir die ID vor dem Start wieder setzen!
            if ( ini_get("session.use_cookies") ) {
                $params = session_get_cookie_params();
                setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
                self::debug("ZSLDDEBUG_ZSLDSESSION_RESTORE_3", json_decode( '{"PARAMS":' .  json_encode( $params ) . '}') );
            }
            
            // Session ID
            session_id( $sid );

            // Session neu starten
            self::session($lifetime);
            self::debug("ZSLDDEBUG_ZSLDSESSION_RESTORE_2", json_decode( '{"STATUS":"2", "SID":"' . $sid . '", "LIFETIME":"' . $lifetime . '"}' ) );
        }
    }
    public static function destroy() { 
        // Session ID
        $sid = session_id();

        // Löschen aller Session-Variablen.
        $_SESSION = array();

        // Falls die Session gelöscht werden soll, löschen wir auch das Session-Cookie.
        // Achtung: Damit wird die Session gelöscht, nicht nur die Session-Daten!
        if ( ini_get("session.use_cookies") ) {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
        }

        // Zum Schluß, löschen der Session.
        // TODO: Prüfen ob die Session file gelöscht werden?
        session_destroy();
    }
    public static function isValid() {
        $valid = FALSE;
        if ( isset( $_SESSION['valid'] ) ) {
            $valid = ( ( new DateTime( $_SESSION['valid'] ) ) > ( new DateTime() ) );
        } 
        return $valid;
    }
    public static function commit() { 
        session_commit();
    }
    public static function reset() { 
        session_reset();
    }
    public static function abort() {
        session_abort();
    }
    public static function write($key, $value) {
        $_SESSION[$key] = $value;
        return self::isValid();
    }
    public static function read($key, $valid = FALSE ) {
        $value = FALSE;
        //check if valid
        if ( $valid || self::isValid() ) {
            if ( isset( $_SESSION[$key] ) ) {
                $value = $_SESSION[$key];
            }
        }
        return $value;
    }
    public static function force($flag = "1", $track = FALSE, $notify = FALSE, $registragion = FALSE) { 
        if ( $track ) {
            setcookie( "TRACKME", $flag, time()+60*60*24*365*100, "/", "zso-aargausued.ch", true, false);
        }
        if ( $notify ) {
            setcookie( "NOTIFYMC", $flag, time()+60*60*24*365*100, "/", "zso-aargausued.ch", true, false);
        }
        if ( $registragion ) {
            setcookie( "REGISTRATIONDIALOG", $flag, time()+30, "/", "zso-aargausued.ch", true, false);
        }
    }
    public static function build($set, $methods) { 
        $self = new self();

        $self->headers($methods);

        if ($set && !isset($_COOKIE["deviceTAC"])) {
            $htac = $self->_htac();
            setcookie( "deviceTAC", $htac, time()+60*60*24*365*100, "/", "zso-aargausued.ch", true, true);
            define( "DEVICE_TAC", $htac );

            $result = $self->registerDevice();
        } else {
            define( "DEVICE_TAC", $_COOKIE["deviceTAC"] );
            $user = null;

            self::session("");
            if ( self::isValid() ) {
                $expiration = "+180 seconds";
            } else {
                $expiration = "-10 seconds";
            }
            self::abort();

            if ( $objects = ObjectStore::parse( DATA_PATH . DATASTORE_DEVICE . '.json' ) ) {
                $response = $objects->list( 0 );
                if ( !empty( $response ) && $response[0]['oid'] === DEVICE_TAC /* && $response[0]['id'] === DEVICE_TAC */ && $response[0]['data'] !==  "unbekannt" ) {
                    /**
                     * Die Angaben über den Benutzer einlesen.
                     */
                    if ( $userdb = ObjectStore::parse( DATA_PATH . DATASTORE_ACCESS . '.json' ) ) {
                        $users = $userdb->list( 0, array( 'rid' => $response[0]['properties']['IPN'] ) );
                        if ( !empty( $users ) ) {
                            //User found in UserDB, session valid. Data access granted
                            $user = $users[0];

                            $today = new DateTime('now');
                            $decision = new DateTime($user['decision']);
                            $decision->add(DateInterval::createFromDateString($user['properties']['Periode']));

                            $duration = ($decision->getTimestamp() - $today->getTimestamp());
                            
                            if ( $duration > 0 ) {
                                $duration = "+" . $duration;
                            }

                            $expiration = $duration . " seconds";

                            self::debug("ZSLDDEBUG_DEVICETAC_BUILD_7", json_decode( '{ "decision": "' . $user['decision'] . '", "periode": "' . $user['properties']['Periode'] . '", "expiration": "' . $expiration . '", "user": ' . json_encode( $user ) . ' }') );
                        } else {
                            //Can't find user in UserDB. Data access prohibited
                            self::debug("ZSLDDEBUG_DEVICETAC_BUILD_6",  json_decode( '{ "error": "Der Benutzer konnte in der Benutzer-Datenbank nicht gefunden werden. Der Zugriff wird verweigert." }' ) );
                        }
                    } else {
                        //Can't parse UserDB. Data access prohibited
                        self::debug("ZSLDDEBUG_DEVICETAC_BUILD_5",  json_decode( '{ "error": "Die Benutzer-Datenbank kann nicht gelesen werden. Der Zugriff wird verweigert." }' ) );
                    }
                } else {
                    if ( empty( $response ) ) {
                        //Device identified but unknown to the backend. Data access prohibited
                        self::debug("ZSLDDEBUG_DEVICETAC_BUILD_4", json_decode( '{ "error": "Das Gerät wurde identifiziert aber nicht in der Geräte-Datenbank gefunden. Das Gerät wurde hinzugefügt. Bitte um erneute Registierung des Gerätes in den nächsten 3 Minuten durch neuladen der Hauptseite." }' ) );
                        
                        $result = $self->registerDevice();

                        $expiration = "+180 seconds";
                    } else {
                        //Device identified but not registered. Data access prohibited
                        self::debug("ZSLDDEBUG_DEVICETAC_BUILD_3", json_decode( '{ "error": "Das Gerät wurde identifiziert aber nicht in der Geräte-Datenbank registriert. Der Zugriff wird verweigert." }' ) );
                    }
                }
            } else {
                //Can't parse ObjectStore. Data access prohibited
                self::debug("ZSLDDEBUG_DEVICETAC_BUILD_2",  json_decode( '{ "error": "Die Geräte-Datenbank kann nicht gelesen werden. Der Zugriff wird verweigert." }' ) );
            }
            self::restore( $expiration ); //$expiration
            self::write( 'expiration', $expiration );
            self::write( 'user', $user );
            self::commit();
        }

        $keys = '';
        if ( $keys = json_decode( file_get_contents( DATA_PATH  . 'registration-popup-key.txt' ) ) ) {
            if ( in_array( DEVICE_TAC, $keys ) ) {
                self::force("1", FALSE, FALSE, TRUE);
            }
        }

    }
}
?>