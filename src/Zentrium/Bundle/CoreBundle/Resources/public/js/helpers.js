var Zentrium = Zentrium || {};

Zentrium.TOKEN = Cookies.get('XSRF-TOKEN');

Zentrium.post = function (url, data) {
  return $.ajax({
    url: url,
    data: data,
    dataType: 'json',
    method: 'POST',
    headers: {
      'X-XSRF-TOKEN': Zentrium.TOKEN
    }
  });
};

Zentrium.addFlash = function (type, message, autoClose) {
  var flashIcons = {'success': 'check', 'warning': 'warning', 'info': 'info'};
  var $flash = $('<div class="alert alert-dismissible"><button type="button" class="close" data-dismiss="alert">×</button></div>').addClass('alert-' + type);
  if(type in flashIcons) {
    $flash.append($('<i class="icon fa"></i>').addClass('fa-' + flashIcons[type]));
  }
  $flash.append(document.createTextNode(message));
  $('.content').prepend($flash);
  if (autoClose) {
    window.setTimeout(function () {
      $flash.slideUp();
    }, 3000);
  }
};
