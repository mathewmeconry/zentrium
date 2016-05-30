$(function() {
  var $view = $('#schedule-requirement-set');
  if(!$view.length) {
    return;
  }
  var config = $view.data('config');

  var generateId = (function () {
    var id = 0;
    return function () {
      return 'tmp' + (++id);
    };
  })();

  var resourceUpdates = {};

  $view.fullCalendar({
    now: config.begin,
    editable: false,
    selectable: !!config.operations,
    selectHelper: true,
    select: function(start, end, jsEvent, view, resource) {
      $view.fullCalendar('unselect');

      var inputStr = prompt(Translator.trans('zentrium_schedule.requirement_set.view.modify_prompt'));
      if(inputStr === null || !inputStr.length) {
        return;
      }
      var operation = (inputStr.charAt(0) != '+' && inputStr.charAt(0) != '-') ? 'set' : 'modify';
      var input = parseInt(inputStr, 10);

      var eventData = {
        id: generateId(),
        resourceId: resource.id,
        title: inputStr,
        start: start,
        end: end,
        className: 'schedule-operation-pending',
      };
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
        timelineView.displayEvents.stop();
        if((resourceUpdates[resource.id] || 0) < data.updated) {
          resourceUpdates[resource.id] = data.updated;
          $view.fullCalendar('removeEvents', function (eventData) {
            return eventData.resourceId == resource.id;
          });
          for(var i in data.requirements) {
            $view.fullCalendar('renderEvent', data.requirements[i]);
          }
        }
        $view.fullCalendar('removeEvents', eventData.id);
        timelineView.displayEvents.start();
      }).fail(function (data) {
        eventData.className = 'schedule-operation-failed';
        $view.fullCalendar('updateEvent', eventData);
      });
    },
    contentHeight: 'auto',
    resourceAreaWidth: '20%',
    scrollTime: '00:00',
    header: false,
    defaultView: 'timelineCustom',
    views: {
      timelineCustom: {
        type: 'timeline',
        duration: { seconds: config.duration },
        slotDuration: { seconds: config.slotDuration },
        slotWidth: 30,
      }
    },
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
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
  });

  timelineView = $view.data('fullCalendar').view;
  timelineView.displayEvents = Zentrium.Schedule.pause(timelineView.displayEvents);
});
