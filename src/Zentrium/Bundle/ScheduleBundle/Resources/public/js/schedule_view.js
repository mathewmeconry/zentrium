$(function() {
  var $view = $('#schedule');
  if (!$view.length) {
    return;
  }
  var config = $view.data('config');

  var options = {
    selectable: false,
    events: config.shifts,
  };
  if (config.layout == 'task') {
    $.extend(options, {
      resourceColumns: [
        {
          labelText: Translator.trans('zentrium_schedule.task.field.name'),
          field: 'name'
        },
        {
          labelText: '',
          field: 'code'
        },
      ],
      resources: config.tasks,
    });
  } else {
    $.extend(options, {
      resourceColumns: [
        {
          labelText: Translator.trans('zentrium_schedule.user.field.name'),
          field: 'name'
        },
      ],
      resources: config.users,
    });
  }

  Zentrium.Schedule.setup($view, config, options);
});
