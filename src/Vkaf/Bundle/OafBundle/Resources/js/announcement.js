import $ from 'jquery';

$(function () {
  $('.oaf-message-info').click(function (e) {
    const $this = $(this);
    e.preventDefault();
    $.get($this.attr('href')).done(info => {
      const $modal = $('#oaf-message-modal').modal();
      const $body = $modal.find('.modal-body');
      $body.children().remove();
      $body.append(info);
    });
  });
});
