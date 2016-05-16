$(function () {
  $('input.form-control.minicolors').minicolors({
    theme: 'bootstrap'
  });

  $('select.form-control').select2({
    minimumResultsForSearch: 10,
    width: '100%'
  });
});
