/**
 * Lagedarstellung globale Koniguration im Script 
 *
 * Die Konfigurationswerte ist im config/settings.php
 */
// Service Worker
var SW_REGISTRATION;
if ('serviceWorker' in navigator && 'PushManager' in window) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('<?php echo SERVICEWORKER;?>')
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

// GeoAdmin Einstellungen
var URL_GA_SEARCH = '<?php echo URL_GA_SEARCH;?>';

// ZSO Lagedarstellung Einstellungen
var URL_ZSLD_SETTINGS = '<?php echo URL_ZSLD_SETTINGS;?>';

// Anzeige Einstellungen
var FADING_DURATION = <?php echo FADING_DURATION;?>;
var DISPLAY_DURATION = <?php echo DISPLAY_DURATION;?>;
