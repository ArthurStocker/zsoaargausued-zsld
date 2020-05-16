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
    function __construct($debug = false) {
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
    public static function session( $lifetime = "+180 seconds" ) { 
        session_set_cookie_params ( $lifetime, "/map/", ".zso-aargausued.ch", TRUE, FALSE );
        session_save_path( DATA_PATH );
        session_name( "ZSLDSESSION" );
        session_start();
        self::write( 'device', DEVICE_TAC );
        self::write( 'valid', date(DATE_ATOM, strtotime( $lifetime , time() ) ) );
    }
    public static function restore( $lifetime = "+3600 seconds" ) { 
        if ( session_status() === 1 ) {
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
            }
            
            // Session ID
            session_id( $sid );

            // Session neu starten
            self::session($lifetime);
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
        // TODO: Zu prüfen! Session file gelöscht werden?
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
    public static function read($key) {
        $value = FALSE;
        //check if valid
        if ( self::isValid() ) {
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

            $response = ObjectStore::save( json_encode( $data, JSON_PRETTY_PRINT ), "device", DEVICE_TAC, false, DATA_PATH . DATASTORE_DEVICE . '.json' );

            //New device. Data access granted for 180 sec
            header("ZSLD-DEBUG: " . '{ "error": "<b>Das Gerät wurde in der Geräte-Datenbank nicht gefunden. Neues Gerät, der Zugriff wird für 180 Sekunden gewährt damit eine Registrierung stattfinden kann.</b>" }' );
            self::session();
        } else {
            define( "DEVICE_TAC", $_COOKIE["deviceTAC"] );

            if ( $objects = ObjectStore::parse( DATA_PATH . DATASTORE_DEVICE . '.json' ) ) {
                $response = $objects->list( 0 );
                if ( !empty( $response ) && $response[0]['oid'] === DEVICE_TAC /* && $response[0]['id'] === DEVICE_TAC */ && $response[0]['data'] !==  "unbekannt" ) {
                    //Session valid. Data access granted
                    //setcookie( "ZSLDDEBUG", "Session valid. Data access granted ", time()+60, "/", "zso-aargausued.ch", false, false);
                    
                    if ( $userdb = ObjectStore::parse( DATA_PATH . DATASTORE_ACCESS . '.json' ) ) {
                        $users = $userdb->list( 0, array( 'rid' => $response[0]['properties']['IPN'] ) );
                        if ( !empty( $users ) ) {
                            header("ZSLD-DEBUG: " . json_encode( $users ) );
                        } else {
                            header("ZSLD-DEBUG: " . '{ "error": "<b>Der Benutzer konnte in der Benutzer-Datenbank nicht gefunden werden. Der Zugriff wird verweigert.</b>" }' );
                        }
                    } else {
                        header("ZSLD-DEBUG: " . '{ "error": "<b>Die Benutzer-Datenbank kann nicht gelesen werden. Der Zugriff wird verweigert.</b>" }' );
                    }
                    self::restore();
                } else {
                    //Device identified but unknown to the backend. Data access prohibited
                    header("ZSLD-DEBUG: " . '{ "error": "<b>Das Gerät wurde in der Geräte-Datenbank gefunden aber nicht registriert. Der Zugriff wird verweigert.</b>" }' );
                }
            } else {
                //Can't parse ObjectStore. Data access prohibited
                header("ZSLD-DEBUG: " . '{ "error": "<b>Die Geräte-Datenbank kann nicht gelesen werden. Der Zugriff wird verweigert.</b>" }' );
            }
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