$(function () {
  $('[data-log-status]').click(function (e) {
    var $this = $(this);
    var status = $this.data('log-status');
    var $endpoint = $this.closest('[data-log-status-endpoint]');
    var endpoint = $endpoint.data('log-status-endpoint');
    e.preventDefault();
    Zentrium.request('PATCH', endpoint, {
      'status': status
    }).done(function () {
      $endpoint.find('li').removeClass('active');
      $this.closest('li').addClass('active');
      $endpoint.find('button i').removeClass().addClass($this.find('i').attr('class'));
    });
  });
});
