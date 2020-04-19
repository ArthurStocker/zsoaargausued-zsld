<?php header('Content-Type: application/javascript');?>
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
