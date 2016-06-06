$(function() {
  var $view = $('#schedule');
  if (!$view.length) {
    return;
  }
  var config = $view.data('config');

  var $modal = $('#shift-edit').modal({
    show: false,
  });
  $modal.data('bs.modal').enforceFocus = $.noop; // https://github.com/select2/select2/issues/600
  var $modalSave = $('#shift-save');
  var $modalDelete = $('#shift-delete');

  var modalSelectData = [];
  Zentrium.request('GET', config.layout == 'task' ? config.users : config.tasks).done(function (data) {
    modalSelectData = $.map(data, function (row) {
      row.text = row.name;
      return row;
    });
  });

  function patchEvent(event, view, data) {
    Zentrium.request((event.endpoint ? 'PATCH' : 'POST'), (event.endpoint || config.endpoint), { shift: data }).done(function (data) {
      view.displayEvents.stop();
      $view.fullCalendar('removeEvents', event.id);
      $view.fullCalendar('renderEvent', data.shift, true);
      view.displayEvents.start();
    }).fail(function () {
      event.className = 'schedule-operation-failed';
      $view.fullCalendar('updateEvent', event);
    });
    event.className = 'schedule-operation-pending';
    event.editable = false;
    if ('_id' in event) {
      $view.fullCalendar('updateEvent', event);
    } else {
      $view.fullCalendar('renderEvent', event, true);
    }
  }

  function editEvent(event, view, data, persistent) {
    $modal.find('h4').text(Translator.trans(config.layout == 'task' ? 'zentrium_schedule.shift.edit.choose_user' : 'zentrium_schedule.shift.edit.choose_task'));

    var $select = $('<select tabindex="100"></select>');
    $modal.find('.modal-body').empty().append($select);
    $select.select2({
      width: '100%',
      data: modalSelectData,
    }).val(event.valueId).trigger('change');

    $modalSave.off();
    $modalSave.click(function () {
      if($select.val() != event.valueId) {
        data = data || {};
        data[config.layout == 'task' ? 'user' : 'task'] = $select.val();
        patchEvent(event, view, data);
      }
      $modal.modal('hide');
    });

    if(persistent) {
      $modalDelete.show();
      $modalDelete.off();
      $modalDelete.click(function () {
        Zentrium.request('DELETE', event.endpoint).done(function () {
          $view.fullCalendar('removeEvents', event.id);
        }).fail(function () {
          event.className = 'schedule-operation-failed';
          $view.fullCalendar('updateEvent', event);
        });
        event.className = 'schedule-operation-pending';
        event.editable = false;
        $view.fullCalendar('updateEvent', event);
        $modal.modal('hide');
      });
    } else {
      $modalDelete.hide();
    }

    $modal.one('shown.bs.modal', function () {
      $select.select2('open');
    });
    $modal.modal('show');
  }

  var options = {
    selectable: !!config.endpoint,
    editable: !!config.endpoint,
    events: config.shifts,
    eventClick: (config.endpoint ? function (event, jsEvent, view) {
      if(event.editable === false) {
        return;
      }
      editEvent(event, view, {}, true);
    } : null),
    eventDrop: function (event, delta, revertFunc, jsEvent, ui, view) {
      var data = {
        from: event.start.format("YYYY-MM-DD[T]HH:mm:ss"),
        to: event.end.format("YYYY-MM-DD[T]HH:mm:ss"),
      };
      data[config.layout == 'task' ? 'task' : 'user'] = event.resourceId;
      patchEvent(event, view, data);
    },
    eventResize: function (event, delta, revertFunc, jsEvent, ui, view) {
      patchEvent(event, view, {
        from: event.start.format("YYYY-MM-DD[T]HH:mm:ss"),
        to: event.end.format("YYYY-MM-DD[T]HH:mm:ss"),
      });
    },
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

  Zentrium.Schedule.setup($view, config, options, function (start, end, jsEvent, view, resource, eventPrototype, updateHelper) {
    var pendingEvent = $.extend({}, eventPrototype, {
      title: '',
    });

    var data = {
      schedule: config.scheduleId,
      from: start.format("YYYY-MM-DD[T]HH:mm:ss"),
      to: end.format("YYYY-MM-DD[T]HH:mm:ss"),
    };
    data[config.layout == 'task' ? 'task' : 'user'] = resource.id;
    editEvent(pendingEvent, view, data, false);
  });
});
