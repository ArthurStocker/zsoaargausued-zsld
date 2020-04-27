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

    function __construct($debug = false) {
        $this->debug = $debug;
 
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
            foreach ($this->allowedOrigins as $allowedOrigin) {
                if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
                    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                    header('Access-Control-Allow-Methods: GET');
                    header('Access-Control-Max-Age: 1000');
                    /*
                    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
                    header('Access-Control-Allow-Credentials: true');
                    */
                    break;
                }
            }
        }

        header('Content-Type: application/javascript');

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: pre-check=0, post-check=0", false);
        header("Pragma: no-cache");
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

self.addEventListener('fetch', event => {
  console.log('Service worker fetching request: ', event.request.url);
});
<?php
    }
}

$worker = new Worker();
$worker->initialize();
?>