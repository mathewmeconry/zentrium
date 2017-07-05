$(function () {
  $('.oaf-desk-resource').click(function (e) {
    var $this = $(this);
    e.preventDefault();
    var target = $this.data('target');
    Zentrium.request('POST', target).done(function (data) {
      $this.replaceWith($('<i>').attr('class', 'fa fa-check'));
    });
  })
})
