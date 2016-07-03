$(function () {
  var $indicator = $('.kiosk-refresh');
  var config = $indicator.data('config');
  if(!_.isObject(config)) {
    return;
  }

  $indicator.css({
    'transition-duration': config.duration + 's',
    'width': '100%'
  });

  window.setTimeout(function () {
    if(config.next) {
      window.location.href = config.next;
    } else {
      window.location.reload();
    }
  }, config.duration * 1000);
});
