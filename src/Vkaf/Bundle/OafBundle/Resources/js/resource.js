import $ from 'jquery';
import { request } from 'zentrium';

$(function () {
  $('.oaf-resource-return').click(function (e) {
    var $this = $(this);
    e.preventDefault();
    var target = $this.data('target');
    request('POST', target).done(function (data) {
      $this.closest('tr').html(data.row);
    }).fail(function () {
      $this.show();
    });
    $this.hide();
  });
});
