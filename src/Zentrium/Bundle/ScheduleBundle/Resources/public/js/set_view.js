$(function() {
  var $view = $('#schedule-requirement-set');
  if(!$view.length) {
    return;
  }
  var config = $view.data('config');

  Zentrium.Schedule.setup($view, config, {
    selectable: !!config.operations,
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
    events: config.requirements,
  }, function (start, end, jsEvent, view, resource, eventPrototype, updateHelper) {
    var inputStr = prompt(Translator.trans('zentrium_schedule.requirement_set.view.modify_prompt'));
    if(inputStr === null || !inputStr.length) {
      return;
    }
    var operation = (inputStr.charAt(0) != '+' && inputStr.charAt(0) != '-') ? 'set' : 'modify';
    var input = parseInt(inputStr, 10);

    var eventData = $.extend({}, eventPrototype, {
      title: inputStr,
    });
    $view.fullCalendar('renderEvent', eventData, true);

    var request;
    if(operation == 'set') {
      request = Zentrium.post(config.operations.set, {
        set_operation: {
          task: resource.id,
          from: start.format("YYYY-MM-DD[T]HH:mm:ss"),
          to: end.format("YYYY-MM-DD[T]HH:mm:ss"),
          count: input,
        }
      });
    } else {
      request = Zentrium.post(config.operations.modify, {
        modify_operation: {
          task: resource.id,
          from: start.format("YYYY-MM-DD[T]HH:mm:ss"),
          to: end.format("YYYY-MM-DD[T]HH:mm:ss"),
          modification: input,
        }
      });
    }
    request.done(function (data) {
      view.displayEvents.stop();
      updateHelper(data.requirements, data.updated);
      $view.fullCalendar('removeEvents', eventData.id);
      view.displayEvents.start();
    }).fail(function (data) {
      eventData.className = 'schedule-operation-failed';
      $view.fullCalendar('updateEvent', eventData);
    });
  });
});
