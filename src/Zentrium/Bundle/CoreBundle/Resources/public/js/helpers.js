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
