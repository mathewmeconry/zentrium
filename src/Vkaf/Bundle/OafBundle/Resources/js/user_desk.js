import $ from 'jquery';
import { request } from 'zentrium';

$(function () {
  $('.oaf-desk-resource').click(function (e) {
    const $this = $(this);
    e.preventDefault();
    request('POST', $this.data('target')).done(function (data) {
      $this.replaceWith($('<i>').attr('class', 'fa fa-check'));
    });
  });
});
