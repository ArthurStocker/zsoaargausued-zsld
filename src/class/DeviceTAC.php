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
    private function registerDevice() {
        //New device. Data access granted for 180 sec
        self::debug("ZSLDDEBUG_DEVICETAC_BUILD_1", json_decode( '{ "error": "Das Gerät wurde in der Geräte-Datenbank nicht gefunden. Neues Gerät, der Zugriff wird für 180 Sekunden gewährt damit eine Registrierung stattfinden kann." }' ) );
        self::session();
        self::write( 'auth', false );
        self::commit();

        $response = '{ "id":"U' . dechex( date_timestamp_get( date_create() ) ) . '", "display":"unbekannt" }';

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
    public static function redirect( $path = False, $parameter = "", $redirect = False ) {
        /*
        // Request method
        $link = $_SERVER["REQUEST_METHOD"];

        $link .=  "&nbsp";

        // Protocol 
        $link .= $_SERVER['SERVER_PROTOCOL'];

        $link .=  "&nbsp";

        // Program to display URL of current page. 
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
            $link .= "https"; 
        else
            $link .= "http"; 

        // Here append the common URL characters. 
        $link .= "://"; 

        // Append the host(domain name, ip) to the URL. 
        $link .= $_SERVER['HTTP_HOST']; 

        $link .= "&nbsp";
        
        // Append the requested resource location to the URL 
        $link .= $_SERVER['REQUEST_URI']; 
        
        // Print the link 
        echo $link; 
        */

        $redirectObject = new stdClass();
        $redirectObject->method = $_SERVER["REQUEST_METHOD"];
        $redirectObject->protocol = $_SERVER["SERVER_PROTOCOL"];
        $redirectObject->transport = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $redirectObject->targethost = $_SERVER['HTTP_HOST'];
        $redirectObject->uri = $_SERVER['REQUEST_URI'];
        $redirectObject->path = ( $path ? $path : ( array_key_exists("redirect", $_GET) ? $_GET["redirect"] : str_replace("?" .$_SERVER['QUERY_STRING'], "", $_SERVER['REQUEST_URI']) ) );
        $redirectObject->query = preg_replace("/redirect_param=(.*?)&/", "", preg_replace("/redirect=(.*?)&/", "", $_SERVER['QUERY_STRING']));
        $redirectObject->param = !strpos($_SERVER['QUERY_STRING'], "redirect_param=") ? "" : str_replace("|", "&", preg_replace("/.*redirect_param=(.*?)&.*/", "?$1", $_SERVER['QUERY_STRING']));
        $redirectObject->location = $redirectObject->transport  . "://" . $redirectObject->targethost . $redirectObject->path . ( $parameter == "" && $redirectObject->query == "" ? "" : "?" ) . ( $parameter != "" ? $parameter . "&" : "" ) . ( $redirectObject->query != "" ? $redirectObject->query : "" );



        if ( !$redirect ) {
            $response = $redirectObject;
        } else {
            $response = $redirect;
            header("Location: " . $redirectObject->location, true, 303);
        }

        return $response;
    }
    public static function debug($key, $message) {
        if (isset($GLOBALS["SUPPORT"])) {
            $GLOBALS["SUPPORT"][$key] = $message;
        }
    }
    public static function session( $lifetime = "+" . SESSION_MIN_DELAYLOCKOUT . " seconds" ) {
        if ( $lifetime !== "" ) { 
            session_set_cookie_params ( "+" . SESSION_GC_MAXLIFETIME . " seconds" /* $lifetime */, "/map/", ".zso-aargausued.ch", TRUE, FALSE );
        }
        session_save_path( DATA_PATH );
        session_name( "ZSLDSESSION" );
        session_start();
        //self::write( 'device', DEVICE_TAC );
        if ( $lifetime !== "" ) {
            self::write( 'valid', date(DATE_ATOM, strtotime( $lifetime , time() ) ) );
        }
    }
    public static function restore( $lifetime = "+" . SESSION_GC_MAXLIFETIME . " seconds" ) { 
        if ( session_status() === 1 ) {
            self::debug("ZSLDDEBUG_ZSLDSESSION_RESTORE_start", json_decode( '{"STATUS": "1"}') );
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
                self::debug("ZSLDDEBUG_ZSLDSESSION_RESTORE_delete_cookie", json_decode( '{"STATUS":"2", "PARAMS":' .  json_encode( $params ) . '}') );
            }
            
            // Session ID
            session_id( $sid );

            // Session neu starten
            self::session($lifetime);
            self::debug("ZSLDDEBUG_ZSLDSESSION_RESTORE_restart", json_decode( '{"STATUS":"2", "SID":"' . $sid . '", "LIFETIME":"' . $lifetime . '"}' ) );
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
    public static function getuser($person) {
        $user = null;
        $duration = 0;
        $expiration = "";

        /**
         * Die Angaben über den Benutzer einlesen.
         */
        if ( $userdb = ObjectStore::parse( DATA_PATH . DATASTORE_ACCESS . '.json' ) ) {
            $users = $userdb->list( 0, array( 'rid' => json_decode( $person, false )->id /* $response[0]['properties']['IPN'] */ ) );
            if ( !empty( $users ) ) {
                //User found in UserDB, session valid. Data access granted
                $user = $users[0];

                $today = new DateTime('now');
                $decision = new DateTime($user['decision']);
                $decision->add(DateInterval::createFromDateString($user['properties']['Periode']));

                $duration = ($decision->getTimestamp() - $today->getTimestamp());
                
                if ( $duration > 0 ) {
                    $expiration = "+" . $duration . " seconds";
                } else {
                    $expiration = $duration . " seconds";
                }

                self::debug("ZSLDDEBUG_DEVICETAC_BUILD_4", json_decode( '{ "decision": "' . $user['decision'] . '", "periode": "' . $user['properties']['Periode'] . '", "expiration": "' . $expiration . '", "user": ' . json_encode( $user ) . ' }') );
            } else {
                //Can't find user in UserDB. Data access prohibited
                self::debug("ZSLDDEBUG_DEVICETAC_BUILD_3",  json_decode( '{ "error": "Der Benutzer konnte in der Benutzer-Datenbank nicht gefunden werden. Der Zugriff wird verweigert." }' ) );
            }
        } else {
            //Can't parse UserDB. Data access prohibited
            self::debug("ZSLDDEBUG_DEVICETAC_BUILD_2",  json_decode( '{ "error": "Die Benutzer-Datenbank kann nicht gelesen werden. Der Zugriff wird verweigert." }' ) );
        }

        return array( "user" => $user, "duration" => $duration, "expiration" => $expiration );
    }
    public static function build($delete, $methods) { 
        $self = new self();

        $self->headers($methods);

        if ($delete && isset($_COOKIE["deviceTAC"])) {
            setcookie( "deviceTAC", $_COOKIE["deviceTAC"], time() - 42000, "/", "zso-aargausued.ch", true, true);
        }

        $user = null;
        $person = null;

        self::session("");
        define( "DEVICE_TAC", session_id());
        if ( self::isValid() ) {
            $expiration = "+" . SESSION_MIN_DELAYLOCKOUT . " seconds";
        } else {
            $expiration = "-10 seconds";
        }
        $person = self::read( 'person' );
        self::abort();

        if ( $person ) {
            $entry = self::getuser( $person );
            $user = $entry['user'];
            $expiration = $entry['expiration'];
        } else {
            $person = $self->registerDevice();
            // $expiration = "+" . SESSION_MIN_DELAYLOCKOUT . " seconds";
        }


        self::restore( $expiration );
        self::write( 'last_access', new DateTime('now') );
        self::write( 'expiration', $expiration );
        self::write( 'person', $person );
        self::write( 'user', $user );
        self::commit();


        $keys = '';
        if ( $keys = json_decode( file_get_contents( DATA_PATH  . 'registration.txt' ) ) ) {
            if ( in_array( DEVICE_TAC, $keys ) ) {
                self::force("1", FALSE, FALSE, TRUE);
            }
        }

        $keys = '';
        if ( $keys = json_decode( file_get_contents( DATA_PATH  . 'tracking.txt' ) ) ) {
            if ( in_array( DEVICE_TAC, $keys ) ) {
                self::force("1", TRUE, TRUE, FALSE);
            }
        }

    }
}
?>