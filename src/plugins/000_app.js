// Show info message
function info(msg) {
  var box = '';
  box += '<div class="alert alert-info alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' + msg + '</div>';
  $("#zsld-alerts").append(box);
}

// Show warn message
function warn(msg) {
  var box = '';
  box += '<div class="alert alert-warning alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' + msg + '</div>';
  $("#zsld-alerts").append(box);
}

// Show danger message
function failed(msg) {
  var box = '';
  box += '<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' + msg + '</div>';
  $("#zsld-alerts").append(box);
}

// Show success message
function passed(msg) {
  var box = '';
  box += '<div class="alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' + msg + '</div>';
  $("#zsld-alerts").append(box);
}
