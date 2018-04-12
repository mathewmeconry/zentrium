import $ from 'jquery';
import Cookies from 'js-cookie';
import Translator from '@willdurand/js-translation-bundle';

export const TOKEN = Cookies.get('XSRF-TOKEN');

export { Translator };

export function request(method, url, data) {
  return $.ajax({
    url: url,
    data: data,
    dataType: 'json',
    method: method,
    headers: {
      'X-XSRF-TOKEN': TOKEN,
    }
  });
};

export function post(url, data) {
  return request('POST', url, data);
};

export function addFlash(type, message, autoClose) {
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
