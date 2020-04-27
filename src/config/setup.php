<?php
class Setup {
    
    private $debug;

    function __construct($debug = false) {
        $this->debug = $debug;
    }

    public function worker() { 
?>
<script>
/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * Die Konfigurationswerte werden durch das config/settings.php geladen
 */
// Service Worker
var SW_REGISTRATION;
if ('serviceWorker' in navigator && 'PushManager' in window) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('<?php echo WORKER;?>')
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
</script>
<?php
    }
}
?>