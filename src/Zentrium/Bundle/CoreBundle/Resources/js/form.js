import $ from 'jquery';

$(function () {
  var language = $('html').attr('lang');

  $('input.form-control.minicolors').minicolors({
    theme: 'bootstrap'
  });

  $('select.form-control').select2({
    minimumResultsForSearch: 10,
    width: '100%'
  });

  $('input.form-control.datepicker-input').each(function () {
    var $this = $(this);
    $this.datepicker({
      autoclose: true,
      format: $this.attr('data-date-format'),
      language: language,
    });
  });

  $('div.form-inline.datepicker-input').each(function () {
    var $this = $(this);
    $this.find('input').first().datepicker({
      autoclose: true,
      format: $this.attr('data-date-format'),
      language: language,
    });
  });
});
