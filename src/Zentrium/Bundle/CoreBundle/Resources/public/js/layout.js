$(function () {
  $('[data-original-title][data-toggle="dropdown"]').tooltip();
});

_init = _.wrap(_init, function (init) {
  init.call(this);

  // Enable click on collapsed menu items
  $.AdminLTE.tree = _.wrap($.AdminLTE.tree, function (tree, menu) {
    if (menu === '.sidebar') {
      $(document).on('click', menu + ' li a', function (e) {
        var $this = $(this);
        if (!$this.next().is('.treeview-menu')) {
          return;
        }
        if (!$this.closest('.sidebar-collapse').length || $this.closest('.sidebar-open').length) {
          return;
        }
        e.preventDefault = $.noop;
      });
    }
    return tree.call(this, menu);
  });
});
