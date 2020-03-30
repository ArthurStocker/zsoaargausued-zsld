/**
 * Allgemeine UI Funktionen und Helper
 * 
 * @todo 
 */
// Endpunkte setzen
function setUrl(name, value) {
  var elRequest = ':input[name=' + name + ']';
  $(elRequest).val(value);
}

// Endpunkte lesen
function getUrl(name) {
  var elRequest = ':input[name=' + name + ']';
  return $(elRequest).val();
}

// Show info message
function info(msg) {
  $('.alert-info').text(msg);
  $('.alert-info').toggle(FADING_DURATION).delay(DISPLAY_DURATION).toggle(FADING_DURATION);
}

// Show danger message
function failed(msg) {
  $('.alert-danger').text(msg);
  $('.alert-danger').toggle(FADING_DURATION).delay(DISPLAY_DURATION).toggle(FADING_DURATION);
}

// Show success message
function passed(msg) {
  $('.alert-success').text(msg);
  $('.alert-success').toggle(FADING_DURATION).delay(DISPLAY_DURATION).toggle(FADING_DURATION);
}
