<?php
require_once 'lib/phpqrcode/qrlib.php';
require_once 'lib/googleauthenticator/GoogleAuthenticator.php';

class UserRegAndAuth {
    
    private $debug;
    private $server;
    private $transaction;
    private $authenticator;

    function __construct($debug = false) {
        $this->debug = $debug;
    }

    public function init() {
        $this->server = $_SERVER['SCRIPT_NAME'];
        $this->transaction = [];
        $this->authenticator = new GoogleAuthenticator();
    }

    public function in( $form = "open" ) {
        $result = '';

        if ($form == "open") {
            $loginForm = '';
            $loginForm .= '<div class="form-group">';
            $loginForm .= '    <label for="username">User ID</label>';
            $loginForm .= '    <input id="username" name="username" type="text" class="form-control" aria-describedby="userHelp" autocomplete="name" placeholder="Benutzername">';
            $loginForm .= '    <small id="userHelp" class="form-text text-muted">Bitte gebe deine Benutzername ein.</small>';
            $loginForm .= '</div>';
            $loginForm .= '<div class="form-group">';
            $loginForm .= '    <label for="password">Password</label>';
            $loginForm .= '    <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" placeholder="Passwort">';
            $loginForm .= '</div>';
            $loginForm .= '<div class="form-group" style="display: none;">';
            $loginForm .= '    <input id="auth" name="auth" type="text" class="form-control" value="login" style="visibility: hidden;">';
            $loginForm .= '</div>';
            
            $result = $loginForm;
        } else {
            $loginButton = '';
            $loginButton .= '<script>';
            $loginButton .= '    $("#modal-login").on("hide.bs.modal", function (e) {';
            $loginButton .= '        $("#zsld-login-form").trigger("reset");';
            $loginButton .= '    });';
            $loginButton .= '    $("#zsld-login-button-cancel").show();';
            $loginButton .= '    if ($("#zsld-login-button-ok").attr("data-dismiss") == "modal" || $("#zsld-login-button-ok").text() == "Ausloggen")';
            $loginButton .= '        $("#zsld-login-button-ok").text("Einloggen").attr("type", "submit").removeAttr("data-dismiss").removeData("dismiss").toggleClass("btn-success", true).toggleClass("btn-default", false);';
            $loginButton .= '</script>';

            $result = $loginButton;
        }

        return $result;
    }

    public function out( $form = "open" ) {
        $result = '';

        if ($form == "open") {
            $logoutForm = '';
            //$logoutForm .= '<div class="form-group">';
            //$logoutForm .= '    <input id="logout" name="logout" type="checkbox" class="form-control" checked style="visibility: hidden;">';
            //$logoutForm .= '</div>';
            $logoutForm .= '<div class="form-group" style="display: none;">';
            $logoutForm .= '    <input id="auth" name="auth" type="text" class="form-control" value="logout" style="visibility: hidden;">';
            $logoutForm .= '</div>';

            $result = $logoutForm;
        } else {
            $logoutButton = '';
            $logoutButton .= '<script>';
            $logoutButton .= '    $("#modal-login").on("hide.bs.modal", function (e) {';
            $logoutButton .= '        $("#zsld-login-form").trigger("reset");';
            $logoutButton .= '    });';
            $logoutButton .= '    $("#zsld-login-button-cancel").show();';
            $logoutButton .= '    if ($("#zsld-login-button-ok").attr("data-dismiss") == "modal" || $("#zsld-login-button-ok").text() == "Einloggen")';
            $logoutButton .= '        $("#zsld-login-button-ok").text("Ausloggen").attr("type", "submit").removeAttr("data-dismiss").removeData("dismiss").toggleClass("btn-success", true).toggleClass("btn-defaull", false);';
            $logoutButton .= '</script>';

            $result = $logoutButton;
        }

        return $result;
    }

    public function act() {
        $result = '';

        $formAction = '';
        $formAction .= '<script>';
        $formAction .= '    $("#zsld-login-form").attr("action", "' . $this->server . '");';
        $formAction .= '    $("#zsld-login-form").submit(function(e) {';
        $formAction .= '        e.preventDefault();';
        $formAction .= '        var url=$(this).closest("form").attr("action");';
        $formAction .= '        var data=$(this).closest("form").serialize();';
        $formAction .= '        $http_login.post(url, data);';
        $formAction .= '    });';
        $formAction .= '</script>';

        $result = $formAction;

        return $result;
    }

    public function msg($impersonated = false) {
        $result = '';

        $closeButton = '';
        $closeButton .= '<script>';

        if ( (int)json_decode( DeviceTAC::read( 'person' ), true )['id'] != (int)DeviceTAC::read( 'user' )['id'] ) {
            $closeButton .= '    $("#zsld-login-impersonated-person").val(' . DeviceTAC::read( 'user' )['person']['display'] . ');';
            $closeButton .= '    $("#zsld-login-impersonated").show();';
        } else {
            $closeButton .= '    $("#zsld-login-impersonated-person").val(' . "''" . ');';
            $closeButton .= '    $("#zsld-login-impersonated").hide();';
        }

        $closeButton .= '    $("#zsld-login-button-cancel").hide();';
        $closeButton .= '    if ($("#zsld-login-button-ok").attr("type") == "submit")';
        $closeButton .= '        $("#zsld-login-button-ok").text("Schliessen").removeAttr("type").attr("data-dismiss", "modal").data("dismiss", "modal").toggleClass("btn-success", false).toggleClass("btn-default", true);';
        $closeButton .= '</script>';

        $result = $closeButton;

        return $result;
    }

    public function log($post) {
        $result = '';

        /**
         * Username und Passwort prüfen
         */
        //$result .= '<script>';
        //$result .= '    console.debug("Device, username and password: ", "' . DEVICE_TAC . '", "' . $post['username'] . '", "' . password_hash($post['password'],  PASSWORD_DEFAULT) . '");';
        //$result .= '</script>';

        /**
         * Rehash Passwort
         */
        //$debug .= '<script>';
        //$debug .= '    console.debug("Rehashed password: ", "' . DeviceTAC::read( 'user' )['properties']['Passwort'] . '");';
        //$debug .= '</script>';

        return $result;
    }

    public function auth($post = array(""), $data = null) {
        $message = '';

        DeviceTAC::restore(  DeviceTAC::read( 'expiration' ) );

        if ( !DeviceTAC::read( 'auth' ) ) {
            $userForm = $this->in( "open" ) . $this->act() . $this->in( "close" );

            if ( isset($post['username']) && $post['username'] != "" && isset($post['password']) && $post['password'] != "" && isset($post['auth']) && $post['auth'] == "login") {
                /**
                 * Der Benutzername und das Passwort wurden angegeben.
                 */
                if ( DeviceTAC::read( 'person' ) || (isset($post["apikey"]) && $post["apikey"] != "" && isset($post["id"]) && $post["id"] != "") ) {
                    if ( DeviceTAC::read( 'person' ) ) {
                        $User = DeviceTAC::getuser( DeviceTAC::read( 'person' ) )['user'];
                    }

                    if (isset($post["apikey"]) && $post["apikey"] != "" && isset($post["id"]) && $post["id"] != "") {
                        $rec = DeviceTAC::getuser( '{ "id": ' . (int)$post["id"] . ' }' );

                        if ($post["apikey"] == $rec['user']['properties']['APIKey']) {
                            $User = $rec['user'];
                            DeviceTAC::write( 'impersonated', (int)$post["id"] );

                            DeviceTAC::commit();
                            DeviceTAC::restore( $rec['expiration'] );
                        } else {
                            /**
                             * Der APIkey ist nicht ungültig.
                             */
                            $userForm = $this->msg();
                            $message .= '<b>Login fehlgeschlagen.</b>';
                
                            $message .= $this->log($post);
                
                            return array("data" => "authentication", "properties" => array("auth" => DeviceTAC::read( 'auth' ), "ui" => $message), "errno" => 403, "error" => "Der APIkey ist nicht ungültig.");
                        }
                    } 

                    DeviceTAC::write( 'user', $User );
                    DeviceTAC::write( 'secret', DeviceTAC::read( 'user' )['properties']['Secret'] );
                    DeviceTAC::write( 'password', DeviceTAC::read( 'user' )['properties']['Passwort'] );

                    if ( DeviceTAC::read( 'user' ) && (int)DeviceTAC::read( 'user' )['properties']['Rechte']['Optionen'] == 255 ) {
                        /**
                         * Der Benutzer ist für das login berechtigt.
                         */
                        if ( DeviceTAC::read( 'user' )['data'] === $post['username'] ) {
                            /**
                             * Der Benutzername ist korrekt.
                             */
                            if ( password_verify( $post['password'], DeviceTAC::read( 'password' ) ) ) {
                                /**
                                 * Das Passwort ist korrekt.
                                 */
                                // TODO: ipersonate hier
                                
                                // update passwort sowie user objekt und speichere die daten in der users db
                                if( password_needs_rehash( DeviceTAC::read( 'password' ), PASSWORD_DEFAULT ) ) {
                                    /**
                                     * Der Hashalgorithmus des gespeicherten Passworts genügt nicht mehr den aktuellen Anforderungen,
                                     * daher sollte es mittels password_hash() neu gehast und anstelle des alten Hashes in
                                     * der Datenbank gespeichert werden:
                                     */
                                    DeviceTAC::write( 'password', password_hash( $post['password'],  PASSWORD_DEFAULT) );

                                    
                                    $User = DeviceTAC::read( 'user' );
                                    $User['properties']['Passwort'] = DeviceTAC::read( 'password' );
        
                                    $User['display'] = $User['data'];
                                    unset($User['data']);

                                    $response = $api->update(json_encode($User, JSON_PRETTY_PRINT), (string)$User->type, (string)constant("DATASTORE_" . strtoupper($User->type)), $User->id);
                
                                    if ( $response->error ) {
                                        /**
                                         * Die Datenbank konnte nicht aktualisiert werden.
                                         */
                                        $message .= $this->msg();
                                        $message .= '<b>Login fehlgeschlagen. Die Datenbank konnte nicht aktualisiert werden.</b>';

                                        $message .= $this->log($post);

                                        return array("data" => "authentication", "properties" => array("auth" => DeviceTAC::read( 'auth' ), "ui" => $message), "errno" => 0, "error" => "");
                                    }
                                }

                                /**
                                 * Die Anmeldung war erfolgreich.
                                 */
                                $userForm = $this->msg();
                                $message .= '<b>Login erfolgreich</b>';

                                DeviceTAC::write( 'auth', true );
                                DeviceTAC::commit();

                                $message .= '<script>';
                                $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
                                $message .= '    Plugins.login.toggle("Logout");';
                                $message .= '</script>';
                            } else {
                                /**
                                 * Die Anmeldung ist fehlgeschlagen.
                                 */
                                $userForm .= '';
                                $message .= '<b>Login fehlgeschlagen. Bitte prüfe die Angaben.</b>';
                                
                                DeviceTAC::write( 'auth', false );
                                DeviceTAC::write( 'impersonated', 0 );
                                DeviceTAC::abort();
                
                                $message .= '<script>';
                                $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
                                $message .= '    Plugins.login.toggle("Login");';
                                $message .= '</script>';
                            }
                        } else {
                            /**
                             * Der Benutzername ist nicht korrekt.
                             */
                            $userForm = $this->msg();
                            $message .= '<b>Login fehlgeschlagen.</b>';
                
                            $message .= $this->log($post);
                
                            return array("data" => "authentication", "properties" => array("auth" => DeviceTAC::read( 'auth' ), "ui" => $message), "errno" => 0, "error" => "");
                        }
                    } else {
                        /**
                         * Der Benutzername ist nicht berechtigt.
                         */
                        $userForm = $this->msg();
                        $message .= '<b>Login fehlgeschlagen.</b>';
            
                        $message .= $this->log($post);
            
                        return array("data" => "authentication", "properties" => array("auth" => DeviceTAC::read( 'auth' ), "ui" => $message), "errno" => 403, "error" => "Der Benutzername ist nicht berechtigt.");
                    }
                } else {
                    /**
                     * Der Benutzername ist nicht registriert und es wurde kein impersonate verlangt.
                     */
                    $userForm = $this->msg();
                    $message .= '<b>Login fehlgeschlagen.</b>';
        
                    $message .= $this->log($post);
        
                    return array("data" => "authentication", "properties" => array("auth" => DeviceTAC::read( 'auth' ), "ui" => $message), "errno" => 403, "error" => "Der Benutzername ist nicht registriert und es wurde kein impersonate verlangt.");
                }
            }

            $message .= $userForm;
            $message .= $this->log($post);
            
        } else {
            $userForm = $this->out( "open" )  . $this->act() . $this->out( "close" );

            if ( isset($post['auth']) && $post['auth'] == "logout" ) {
                /**
                 * Die Abmeldung war erfolgreich.
                 */
                $userForm = $this->msg();
                $message .= '<b>Logout erfolgreich</b>';
        
                DeviceTAC::write( 'auth', false );
                DeviceTAC::write( 'impersonated', 0 );
                DeviceTAC::commit();
        
                $message .= '<script>';
                $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
                $message .= '    Plugins.login.toggle("Login");';
                $message .= '</script>';
        
            } else {
                /**
                 * Die Abmeldung ist fehlgeschlagen.
                 */
                $userForm .= '';
                $message .= '<b>Du bist Eingeloggt</b>';
                
                DeviceTAC::write( 'auth', true );
                DeviceTAC::abort();
        
                $message .= '<script>';
                $message .= '    AUTH = ' . ( ( is_bool( DeviceTAC::read( 'auth' ) ) && DeviceTAC::read( 'auth' ) ) ? 'true' : 'false' ) . ';';
                $message .= '    Plugins.login.toggle("Logout");';
                $message .= '</script>';
        
            }
        
            $message .= $userForm;
            $message .= $this->log($post);

        }

        return array("data" => "authentication", "properties" => array("auth" => DeviceTAC::read( 'auth' ), "ui" => $message), "errno" => 0, "error" => "");
    }

    public function otpauth($get, $data) {
        $this->transaction['data'] = "OTPAuth";

        if (isset($get['otpauth']) && (string)$get['otpauth'] == "qrcode")  {
            $data = json_decode($data, true);
    
            if ( isset($data['display']) && $data['display'] != "" && isset($data['key']) /* && $data['key'] != "" */ && isset($data['id']) && isset($data['properties']) && isset($data['properties'][PUBLIC_ID]) && $data['properties'][PUBLIC_ID] == $data['id'] ) {
                $Issuer = "zso-aargausued.ch";
                $Username = $data['display'];
    
                DeviceTAC::restore( DeviceTAC::read( 'expiration' ) );
    
                // check if user exists and get data
                $User = DeviceTAC::getuser( '{ "id":' . $data['properties']['IPN'] . ' }' )["user"];
                if ( $User && isset($User["properties"]["Secret"]) ) {
                    $Secret = $User["properties"]["Secret"];
                } else {
                    $Secret = DeviceTAC::read( 'secret' );
                }
    
                if ( !isset($Secret) || $Secret == "" ) {
                    $Secret = $this->authenticator->generateSecret();
                    
                    $OTPAuthURL = 'otpauth://totp/'.$Issuer.':'.$Username.'?secret='.$Secret.'&issuer='.$Issuer;
    
                    ob_start();
                        QRCode::png($OTPAuthURL, null, QR_ECLEVEL_M, 5, 0, false);
                        $OTPAuthQRCode = base64_encode(ob_get_contents()); 	//<img src="data:image/png;base64,'.$OTPAuthQRCode.'">
                    ob_end_clean();
                } else {
                    $OTPAuthURL = "";
                    $OTPAuthQRCode = "";
                }
    
                if ( isset($data['key']) && $data['key'] != "" ) {
                    $Password = password_hash($data['key'],  PASSWORD_DEFAULT);
                } else {
                    $Password = password_hash($Secret,  PASSWORD_DEFAULT);
                }
    
                DeviceTAC::write( 'secret', $Secret );
                DeviceTAC::write( 'password', $Password );
                DeviceTAC::commit();
    
                $this->transaction['data'] = "success";
                $this->transaction['properties'] = [];
                $this->transaction['properties']['key'] = $Password;
                $this->transaction['properties']['OTPAuthURL'] = $OTPAuthURL;
                $this->transaction['properties']['OTPAuthQRCode'] = $OTPAuthQRCode;
                $this->transaction['executingdevice'] = session_id();
                $this->transaction['errno'] = 0;
                $this->transaction['error'] = "";
            } else {
                $this->transaction['data'] = "failed";
                $this->transaction['properties'] = [];
                $this->transaction['properties']['key'] = "";
                $this->transaction['properties']['OTPAuthURL'] = ""; 
                $this->transaction['properties']['OTPAuthQRCode'] = "";
                $this->transaction['executingdevice'] = session_id();
                $this->transaction['errno'] = 422;
                $this->transaction['error'] = "missing data";
            }
        } elseif (isset($get['otpauth']) && (string)$get['otpauth'] == "sms")  {
            $User = DeviceTAC::getuser( $data )["user"];
            $Secret = DeviceTAC::read( 'secret' );

            if (isset(json_decode($data, true)["overwrite"]) && json_decode($data, true)["overwrite"] == "secret") {
                $Secret = $User["properties"]["Secret"];
            }
    
            if ( !$User ) {
                $User["person"] = json_decode($data, true);
            }

            if ( $User && isset($User["person"]["properties"]["Mobilnummer"]) && isset($Secret) && $Secret != "" ) {
                //The url you wish to send the POST request to
                $url = SMS_URL;
        
                //The data you want to send via POST
                $json = json_encode([
                    "UserName" => SMS_ID,
                    "Password" => SMS_PW,
                    "Originator" => "ZSO-Kommando",
                    "Recipients" => array(preg_replace('/\s+/', '', $User["person"]["properties"]["Mobilnummer"])),
                    "MessageText" => (string)$this->authenticator->getCode($Secret),
                    "ForceGSM7bit" => true
                ]);
        
                //open connection
                $smsblaster = curl_init();
        
                //set the url, number of POST vars, POST data
                curl_setopt($smsblaster,CURLOPT_URL, $url);
                curl_setopt($smsblaster,CURLOPT_POST, true);
                curl_setopt($smsblaster,CURLOPT_POSTFIELDS, $json);
                curl_setopt($smsblaster,CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type:application/json',
                        'Content-Length: ' . strlen($json)
                    )
                );
        
                //So that curl_exec returns the contents of the cURL; rather than echoing it
                curl_setopt($smsblaster,CURLOPT_RETURNTRANSFER, true); 
        
                //execute post
                $this->transaction['data'] = curl_exec($smsblaster);
                $this->transaction['properties'] = [];
                /*
                $this->transaction['properties']['person'] = $User["person"];
                $this->transaction['properties']['mobile'] = preg_replace('/\s+/', '', $User["person"]["properties"]["Mobilnummer"]);
                $this->transaction['properties']['json'] = $json;
                $this->transaction['properties']['code'] = curl_getinfo($smsblaster, CURLINFO_HTTP_CODE);
                */
                $this->transaction['executingdevice'] = session_id();
                $this->transaction['errno'] = 0;
                $this->transaction['error'] = "";
            } else {
                $this->transaction['data'] = "failed";
                $this->transaction['properties'] = [];
                /*
                $this->transaction['properties']['user'] = $User;
                $this->transaction['properties']['data'] = $data;
                $this->transaction['properties']['secret'] = $Secret;
                */
                $this->transaction['executingdevice'] = session_id();
                $this->transaction['errno'] = 422;
                $this->transaction['error'] = "method not defined";
            }
        } else {
            $this->transaction['data'] = "failed";
            $this->transaction['properties'] = [];
            $this->transaction['executingdevice'] = session_id();
            $this->transaction['errno'] = 422;
            $this->transaction['error'] = "method not defined";
        }

        return $this->transaction;
    }

    public function registration($get, $data) {
        $api = new Rest();

        $data = json_decode($data, false);

        $otp = $data->otp;
    
        $json = $data;
        unset($json->otp);
        $json = json_encode( $json );
    
        $Secret = DeviceTAC::read( 'secret' );
    
        if ( $this->authenticator->getCode($Secret) == $otp ) {
            DeviceTAC::restore();
            DeviceTAC::write( 'person', $json );
    
            if (array_key_exists('type', $get) && isset($get['type']) && (string)$get['type'] == "access") {
                $Person = DeviceTAC::getuser( $json );
    
                if ( $Person["user"] ) {
                    $User = $Person["user"];

                    if ( $Person["duration"] < 60*60*24*USER_AUTO_LOCKOUT ) {
                        $User['properties']['Periode'] = "+" . USER_AUTO_LOCKOUT . " days";
    
                        if ( $Person["duration"] <= 0 ) {
                            $User['properties']['Rechte']['Imitieren'] = 0;
                            $User['properties']['Rechte']['Optionen'] = 254;
                            $User['properties']['Rechte']['Berichte'] = 0;
                        }
                    }
    
                    $User['properties']['Passwort'] = DeviceTAC::read( 'password' );
    
                    $User['display'] = $User['data'];
                    unset($User['data']);

                    $result = $api->update(json_encode($User, JSON_PRETTY_PRINT), (string)$User['type'], (string)constant("DATASTORE_" . strtoupper($User['type'])), $User['id']);
                } else {
                    $User = new stdClass();
                    $User->id = $data->id;
                    $User->type = (string)$get['type'];
                    $User->display = $data->properties->Name.$data->properties->Vorname;
                    $User->properties = new stdClass();
                    $User->properties->Rechte = new stdClass();
                    $User->properties->Rechte->Imitieren = 0;
                    $User->properties->Rechte->Optionen = 254;
                    $User->properties->Rechte->Berichte = 0;
                    $User->properties->APIkey = password_hash( $this->randomPassword(),  PASSWORD_DEFAULT );
                    $User->properties->Secret = DeviceTAC::read( 'secret' );
                    $User->properties->Periode = "+" . USER_AUTO_LOCKOUT . " days";
                    $User->properties->Passwort = DeviceTAC::read( 'password' );
                    $User->forceconcurrentobjects = false;
                    $User->concurrentobjectsallowed = false;
    
                    $result = $api->create(json_encode($User, JSON_PRETTY_PRINT), (string)$User->type, (string)constant("DATASTORE_" . strtoupper($User->type)), $User->id);
                }
    
                $this->transaction['display'] = $result['data'];
                $this->transaction['executingdevice'] = $result['executingdevice'];
                $this->transaction['result'] = "success";
                $this->transaction['errno'] = $result['errno'];
                $this->transaction['error'] = $result['error'];
            } else {
                $this->transaction['display'] = $data['display'];
                $this->transaction['executingdevice'] = session_id();
                $this->transaction['result'] = "failed";
                $this->transaction['errno'] = 0;
                $this->transaction['error'] = "permissions not set";
            }
    
            DeviceTAC::commit();
        } else {
            $this->transaction['data'] = [json_encode($data), json_encode($otp), $json];
            $this->transaction['display'] = $data['display'];
            $this->transaction['executingdevice'] = session_id();
            $this->transaction['result'] = "failed";
            $this->transaction['errno'] = 422;
            $this->transaction['error'] = "permissions not set, otp failure";
        }

        return $this->transaction;
    }

    public function randomPassword() {
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = 7;
        $d = rand(0, $alphaLength);
        do {
            $s = rand(0, $alphaLength);
        } while ( $s === $d );
        for ($i = 0; $i < 8; $i++) {
            if ( $i === $d ) {
                $pass[] = $this->randomNumber();
            } else if ( $i === $s ) {
                $pass[] = $this->randomSpecChar(); 
            } else {
                if ( rand(0, 1) === 1 )  {
                    $pass[] = $this->randomUpper();
                } else {
                    $pass[] = $this->randomLower();
                }
            }
        }
        return implode($pass); //turn the array into a string
    }
        
    private function randomUpper() {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        $n = rand(0, $alphaLength);
        return $alphabet[$n]; //turn the array into a string
    }
    private function randomLower() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        $n = rand(0, $alphaLength);
        return $alphabet[$n]; //turn the array into a string
    }
    
    private function randomNumber() {
        $alphabet = '1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        $n = rand(0, $alphaLength);
        return $alphabet[$n]; //turn the array into a string
    }
    
    private function randomSpecChar() {
        $alphabet = '+*%&$?';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        $n = rand(0, $alphaLength);
        return $alphabet[$n]; //turn the array into a string
    }
}
?>