import $ from 'jquery';
import { Translator } from 'zentrium';

$(function () {
  $('[data-original-title][data-toggle="dropdown"]').tooltip();

  $('.sidebar-menu').on('click', '.treeview a', function (e) {
    const $this = $(this);
    if ($this.closest('.sidebar-collapse').length && !$this.closest('.sidebar-open').length) {
      e.stopImmediatePropagation();
    }
  }).tree();

  $('.table-searchable').each(function () {
    const $table = $(this);
    const $inputRow = $('<tr><td><input class="form-control"></td></tr>');
    const $input = $inputRow.find('input');
    $table.children('thead').prepend($inputRow);
    $input.attr('placeholder', Translator.trans('zentrium.search'))
    $input.on('input', function () {
      const query = $(this).val().toLowerCase().trim().split(/\s+/);
      $table.children('tbody').children().each(function () {
        const $row = $(this);
        if (query.length === 1 && query[0] === '') {
          $row.show();
          return;
        }
        const text = $row.text().toLowerCase();
        for (let token of query) {
          if (text.indexOf(token) === -1) {
            $row.hide();
            return;
          }
        }
        $row.show();
      })
    });
  });
});
