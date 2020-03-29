/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * Die Konfigurationswerte ist im config/settings.php
 */
// Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('includes/lib/zsld-service-worker.js')
    .then(registration => {
      console.log('SW registered with scope:', registration.scope);
    })
    .catch(err => {
      console.error('Registration failed:', err);
    });
  });
}

// Anzeige Einstellungen
var FADING_DURATION = <?php echo FADING_DURATION;?>;
var DISPLAY_DURATION = <?php echo DISPLAY_DURATION;?>;
