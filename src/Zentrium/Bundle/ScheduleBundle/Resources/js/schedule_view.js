import $ from 'jquery';
import _ from 'underscore';
import crosstab from 'crosstab';
import moment from 'moment';
import { request, Translator } from 'zentrium';
import { setup } from './utils';

$(function() {
  const $view = $('#schedule');
  if (!$view.length) {
    return;
  }
  const config = $view.data('config');

  const $modal = $('#shift-edit').modal({
    show: false,
  });
  $modal.data('bs.modal').enforceFocus = $.noop; // https://github.com/select2/select2/issues/600
  const $modalSave = $('#shift-save');
  const $modalDelete = $('#shift-delete');
  const $modalTimesheet = $('#shift-timesheet');
  $modalTimesheet.mouseup(function (e) {
    if(e.which != 3) {
      $modal.modal('hide');
    }
  });

  let modalSelectData = [];
  request('GET', config.layout == 'task' ? config.users : config.tasks).done(function (data) {
    modalSelectData = $.map(data, function (row) {
      row.text = row.name;
      if (row.groups && row.groups.length) {
        row.text += ' (' + row.groups.join(', ') + ')';
      }
      return row;
    });
  });

  function patchEvent(event, view, data) {
    request((event.endpoint ? 'PATCH' : 'POST'), (event.endpoint || config.endpoint), { shift: data }).done(function (data) {
      view.displayEvents.stop();
      $view.fullCalendar('removeEvents', event.id);
      $view.fullCalendar('renderEvent', data.shift, true);
      view.displayEvents.start();
      if(crosstab.supported) {
        crosstab.broadcast('schedule:change', { id: config.scheduleId });
      }
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

    const resource = $view.fullCalendar('getResourceById', event.resourceId);
    const $warningIcon = $('<i class="fa fa-warning"></i>').attr('title', Translator.trans('zentrium_schedule.shift.edit.insufficient_skills'));
    const $select = $('<select tabindex="100"></select>');
    $modal.find('.modal-body').empty().append($select);
    $select.select2({
      width: '100%',
      data: modalSelectData,
      templateResult: function (state) {
        const $result = $('<span></span>').text(state.text);
        if (
          (resource.skill && !_.findWhere(state.skills, { id: resource.skill})) ||
          (state.skill && !_.findWhere(resource.skills, { id: state.skill }))
        ) {
          $result.append($warningIcon.clone());
        }
        return $result;
      },
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
        request('DELETE', event.endpoint).done(function () {
          $view.fullCalendar('removeEvents', event.id);
          if(crosstab.supported) {
            crosstab.broadcast('schedule:change', { id: config.scheduleId });
          }
        }).fail(function () {
          event.className = 'schedule-operation-failed';
          $view.fullCalendar('updateEvent', event);
        });
        event.className = 'schedule-operation-pending';
        event.editable = false;
        $view.fullCalendar('updateEvent', event);
        $modal.modal('hide');
      });
      $modalTimesheet.show();
      $modalTimesheet.attr('href', event.timesheet);
    } else {
      $modalDelete.hide();
      $modalTimesheet.hide();
    }

    if(moment().diff(event.start) < 0) { // event has not started yet
      $modal.one('shown.bs.modal', function () {
        $select.select2('open');
      });
    }
    $modal.modal('show');
  }

  const eventSources = [{
    url: config.shifts
  }];
  if(config.layout == 'user') {
    eventSources.push({
      url: config.availabilities
    });
  }

  const editable = _.isString(config.endpoint);
  const options = {
    selectable: editable,
    editable: editable,
    eventSources: eventSources,
    eventClick: (editable ? function (event, jsEvent, view) {
      if(event.editable === false) {
        return;
      }
      editEvent(event, view, {}, true);
    } : null),
    eventDrop: function (event, delta, revertFunc, jsEvent, ui, view) {
      const data = {
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
      resourceRender: function (resource, $columns, $cells) {
        const $row = $columns.first().parent();
        $row.tooltip({
          title: resource.notes,
          placement: 'right',
          container: 'body',
        });
      },
      resources: config.tasks,
    });
  } else {
    $.extend(options, {
      resourceColumns: [
        {
          labelText: Translator.trans('zentrium_schedule.user.field.name'),
          text: function (resource) {
            if(resource.groups.length) {
              return resource.name + ' (' + resource.groups.join(', ') + ')';
            } else {
              return resource.name;
            }
          }
        }
      ],
      resourceRender: function (resource, $columns, $cells) {
        const $cellContent = $columns.first().find('.fc-cell-content');

        const $actions = $('<span class="schedule-column-actions"></span>');
        $actions.append($('<a><i class="fa fa-check-circle"></i></a>').attr('href', resource.availability).attr('title', Translator.trans('zentrium_schedule.schedule.view.user_availability')));
        for(let skill of resource.skills) {
          $actions.prepend($('<span class="label label-primary"></span>').text(skill.name));
        }
        $cellContent.prepend($actions);

        const $row = $columns.first().parent();
        $row.tooltip({
          title: resource.notes,
          placement: 'right',
          container: 'body',
        });
      },
      resources: config.users,
    });
  }

  setup($view, config, options, function (start, end, jsEvent, view, resource, eventPrototype, updateHelper) {
    const pendingEvent = $.extend({}, eventPrototype, {
      title: '',
    });

    const data = {
      schedule: config.scheduleId,
      from: start.format("YYYY-MM-DD[T]HH:mm:ss"),
      to: end.format("YYYY-MM-DD[T]HH:mm:ss"),
    };
    data[config.layout == 'task' ? 'task' : 'user'] = resource.id;
    editEvent(pendingEvent, view, data, false);
  });

  if(crosstab.supported) {
    setInterval(function () {
      crosstab.broadcast('schedule:advertise', {
        id: config.scheduleId,
        name: config.name,
        begin: moment(config.begin).valueOf(),
        slotDuration: config.slotDuration,
        slotWidth: $view.fullCalendar('getView').slotWidth,
      });
    }, 2000);

    const $scrollers = $view.find('.fc-time-area .fc-scroller');
    $scrollers.on('scroll', _.throttle(function () {
      const scroll = $view.fullCalendar('getView').queryScroll();
      crosstab.broadcast('schedule:scroll', {
        id: config.scheduleId,
        top: scroll.top,
        left: scroll.left
      });
    }, 500, { leading: false }));
  }
});
