import $ from 'jquery';
import _ from 'underscore';
import crosstab from 'crosstab';
import moment from 'moment'
import screenfull from 'screenfull';
import { post, Translator } from 'zentrium';
import { setup } from './utils';

$(function() {
  const $view = $('#schedule-requirement-set');
  if(!$view.length) {
    return;
  }
  const config = $view.data('config');

  setup($view, config, {
    selectable: !_.isEmpty(config.operations),
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
    events: config.requirements,
  }, function (start, end, jsEvent, view, resource, eventPrototype, updateHelper) {
    const inputStr = prompt(Translator.trans('zentrium_schedule.requirement_set.view.modify_prompt'));
    if(inputStr === null || !inputStr.length) {
      return;
    }
    const operation = (inputStr.charAt(0) != '+' && inputStr.charAt(0) != '-') ? 'set' : 'modify';
    const input = parseInt(inputStr, 10);

    const eventData = $.extend({}, eventPrototype, {
      title: inputStr,
    });
    $view.fullCalendar('renderEvent', eventData, true);

    let request;
    if(operation == 'set') {
      request = post(config.operations.set, {
        set_operation: {
          task: resource.id,
          from: start.format("YYYY-MM-DD[T]HH:mm:ss"),
          to: end.format("YYYY-MM-DD[T]HH:mm:ss"),
          count: input,
        }
      });
    } else {
      request = post(config.operations.modify, {
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

  if(screenfull.enabled) {
    const $fullscreenBtn = $('<button class="btn btn-box-tool"><i class="fa fa-arrows-alt"></i></button>');
    $fullscreenBtn.tooltip({
      title: Translator.trans('zentrium.fullscreen.enter')
    });
    $fullscreenBtn.click(function () {
      screenfull.request($view[0]);
      return false;
    });
    $('.box-primary .box-tools').append($fullscreenBtn);
  }

  if(crosstab.supported) {
    const activeSchedule = {};
    const schedules = {};
    const timeouts = {};
    const slotWidth = $view.fullCalendar('getView').slotWidth;
    const beginSerialized = moment(config.begin).valueOf();

    const $menu = $('<div class="btn-group"><button data-toggle="dropdown" class="btn btn-box-tool"><i class="fa fa-link"></i></button><ul class="dropdown-menu"></ul></div>');
    $menu.find('button').attr('data-title', Translator.trans('zentrium_schedule.requirement_set.view.synchronization.start')).tooltip();
    const $stop = $('<button class="btn btn-box-tool" data-toggle="dropdown"><i class="fa fa-unlink"></i></button>');
    $stop.attr('data-title', Translator.trans('zentrium_schedule.requirement_set.view.synchronization.stop')).tooltip();
    $stop.click(function () {
      activeSchedule.id = null;
      updateMenu();
    });
    $('.box-primary .box-tools').prepend($menu).prepend($stop);

    const updateMenu = function () {
      if(activeSchedule.id) {
        $menu.hide();
        $stop.show();
      } else if(Object.values(schedules).length) {
        const $ul = $menu.find('ul');
        $ul.empty();
        for(let schedule of Object.values(schedules)) {
          $ul.append($('<li></li>').append($('<a></a>').text(schedule.name).data('id', schedule.id)));
        }
        $ul.on('click', 'a', function () {
          const id = $(this).data('id');
          activeSchedule.id = id;
          updateMenu();
        });
        $menu.show();
        $stop.hide();
      } else {
        $menu.hide();
        $stop.hide();
      }
    };

    updateMenu();

    crosstab.on('schedule:advertise', function (message) {
      const schedule = message.data;
      if(schedule.begin != beginSerialized || schedule.slotWidth != slotWidth || schedule.slotDuration != config.slotDuration) {
        return;
      }
      schedules[schedule.id] = schedule;
      if(schedule.id in timeouts) {
        clearTimeout(timeouts[schedule.id]);
      }
      timeouts[schedule.id] = setTimeout(function () {
        if(activeSchedule.id == schedule.id) {
          activeSchedule.id = null;
        }
        delete schedules[schedule.id];
        updateMenu();
      }, 4000);
      updateMenu();
    });

    crosstab.on('schedule:change', function (message) {
      if(message.data.id == activeSchedule.id) {
        $view.fullCalendar('refetchEvents');
      }
    });

    crosstab.on('schedule:scroll', function (message) {
      if(message.data.id == activeSchedule.id) {
        const view = $view.fullCalendar('getView');
        const oldScroll = view.queryScroll();
        view.applyScroll({ top: oldScroll.top, left: message.data.left });
      }
    });
  }
});
