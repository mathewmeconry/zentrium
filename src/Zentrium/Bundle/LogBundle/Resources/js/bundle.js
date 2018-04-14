import $ from 'jquery';
import { request } from 'zentrium';

$(function () {
  $('[data-log-status]').click(function (e) {
    const $this = $(this);
    const status = $this.data('log-status');
    const $endpoint = $this.closest('[data-log-status-endpoint]');
    const endpoint = $endpoint.data('log-status-endpoint');
    e.preventDefault();
    request('PATCH', endpoint, { status }).done(function () {
      $endpoint.find('li').removeClass('active');
      $this.closest('li').addClass('active');
      $endpoint.find('button i').removeClass().addClass($this.find('i').attr('class'));
    });
  });
});
