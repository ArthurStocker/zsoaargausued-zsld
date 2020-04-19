<?php

class ZSLDTAC {
    
    private $debug;

    public static function build($set) { 
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

        if ($set && !isset($_COOKIE["deviceTAC"])) {
            setcookie ( "deviceTAC", $htac, time()+60*60*24*365*100, "/", "zso-aargausued.ch", true, true);
            define("DEVICE_TAC", $htac);
        } else {
            define("DEVICE_TAC", $_COOKIE["deviceTAC"]);
        }
    }
}
?>