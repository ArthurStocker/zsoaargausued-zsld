// general post-loading scripts
$(document).ready(function() {
  var isClosed = true;
  var trigger = $('.hamburger');
  var overlay = $('.overlay');

  trigger.click(sidebar_toggle);

  function sidebar_toggle() {

    if (isClosed == true) {
      overlay.hide();
      trigger.removeClass('is-open');
      trigger.addClass('is-closed');
      isClosed = false;
    } else {
      overlay.show();
      trigger.removeClass('is-closed');
      trigger.addClass('is-open');
      isClosed = true;
    }
  }

  $('[data-toggle="offcanvas"]').click(function() {
    $('#wrapper').toggleClass('toggled');
  });

  sidebar_toggle();
  $('#wrapper').toggleClass('toggled')
  $('.menu-toggle').hide();
});
