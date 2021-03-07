<?php
chdir("map/");

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require_once 'config/settings.php';

class Worker {
    
    private $debug;

    // array holding allowed Origin domains
    private $allowedOrigins = array(
        '(http(s)://)?(www\.)?zso-aargausued\.ch' //,
        /*
        '(http(s)://)?(www\.)?codepen\.io',
        '(http(s)://)?(www\.)?cdpn\.io',
        */
    );

    function __construct($debug = false, $methods = "GET", $cache = 86400 /* 86400 cache for 1 day */) {
        $this->debug = $debug;

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
            foreach ($this->allowedOrigins as $allowedOrigin) {
                if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
                    header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
                    //header('Access-Control-Allow-Credentials: true');
                    header('Access-Control-Max-Age: ' . $cache);
                    header("Vary: Origin");
                }
            }
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: " . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
            } else {
                header("Access-Control-Allow-Methods: " . $methods);
            }
            /*
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: " . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
            } else {
                header("Access-Control-Allow-Headers: " . $headers);
            }
            */

            exit(0);
        }

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: pre-check=0, post-check=0", false);
        header("Pragma: no-cache");

        header('Content-Type: application/javascript');
    }

    public function initialize() { 
?>
/**
 * Lagedarstellung globale Konfiguration im Script 
 *
 * Die Konfigurationswerte werden durch das config/settings.php geladen
 */
// Service Worker
self.addEventListener('install', event => {
  console.log('Service worker installing...');
  // Skip waiting for activation
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  console.log('Service worker activating...');
});

//self.addEventListener('fetch', event => {
//  console.log('Service worker fetching request: ', event.request.url);
//});
<?php
    }
}

$worker = new Worker();
$worker->initialize();
?>