import $ from 'jquery';

$(function () {
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-original-title][data-toggle="dropdown"]').tooltip();

  $('.sidebar-menu').on('click', '.treeview a', function (e) {
    const $this = $(this);
    if ($this.closest('.sidebar-collapse').length && !$this.closest('.sidebar-open').length) {
      e.stopImmediatePropagation();
    }
  }).tree();
});
