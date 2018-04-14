import $ from 'jquery';
import { getViewConfig, ResourceTimelineView as BaseResourceTimelineView } from 'fullcalendar';

class ResourceTimelineView extends BaseResourceTimelineView {
  slatCellHtml(date, isEm) {
    const html = super.slatCellHtml(date, isEm);
    const classes = [];
    if(date.hour() === 0 && date.minute() === 0) {
      classes.push('schedule-slat-day');
    }
    if(date.hour() % 6 === 0 && date.minute() === 0) {
      classes.push('schedule-slat-quarter');
    }
    return html.replace('class="', 'class="' + classes.join(' ') + ' ');
  }
}

getViewConfig('timeline').resourceClass = ResourceTimelineView;

export function pause(func, paused) {
  let lastArgs;
  let lastThis;
  let queue = false;

  function run() {
    if(!paused) {
      func.apply(this, arguments);
    } else {
      queue = true;
      lastArgs = arguments;
      lastThis = this;
    }
  }

  run.start = function () {
    paused = false;
    if(queue) {
      func.apply(lastThis, lastArgs);
      queue = false;
      lastArgs = undefined;
      lastThis = undefined;
    }
  };

  run.stop = function () {
    paused = true;
  };

  return run;
};

export function setup($view, parameters, config, selectCallback) {
  if(!$view.length) {
    return;
  }

  const generateId = (function () {
    let id = 0;
    return function () {
      return 'tmp' + (++id);
    };
  })();

  const resourceUpdates = {};

  $view.fullCalendar($.extend({}, {
    editable: false,
    selectable: false,
    selectHelper: true,
    select: function(start, end, jsEvent, view, resource) {
      $view.fullCalendar('unselect');

      const eventPrototype = {
        id: generateId(),
        resourceId: resource.id,
        start: start,
        end: end,
        className: ['schedule-operation-pending'],
        editable: false,
      };

      const updateHelper = function (events, time) {
        if(!time || (resourceUpdates[resource.id] || 0) < time) {
          resourceUpdates[resource.id] = time;
          $view.fullCalendar('removeEvents', function (eventData) {
            return eventData.resourceId == resource.id;
          });
          for(let event of events) {
            $view.fullCalendar('renderEvent', event);
          }
        }
      };

      selectCallback(start, end, jsEvent, view, resource, eventPrototype, updateHelper);
    },
    contentHeight: 'auto',
    resourceAreaWidth: '20%',
    scrollTime: '00:00',
    header: false,
    defaultView: 'timelineCustom',
    defaultDate: parameters.begin || null,
    now: $.fullCalendar.moment(),
    nowIndicator: true,
    views: {
      timelineCustom: {
        type: 'timeline',
        duration: { seconds: parameters.duration || 0 },
        slotDuration: { seconds: parameters.slotDuration || 0 },
        slotWidth: 30,
      }
    },
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
  }, config));

  const view = $view.fullCalendar('getView');
  view.displayEvents = pause(view.displayEvents);

  $(window).keydown(function(e) {
    if(e.keyCode != 37 && e.keyCode != 39) { // left/right arrow keys
      return;
    }
    const view = $view.fullCalendar('getView');
    const slotWidth = view.slotWidth;
    const oldScroll = view.queryScroll();
    const newScroll = { top: oldScroll.top };
    if(e.keyCode == 37) {
      newScroll.left = Math.round((oldScroll.left - slotWidth) / slotWidth) * slotWidth;
    } else {
      newScroll.left = Math.round((oldScroll.left + slotWidth) / slotWidth) * slotWidth;
    }
    view.applyScroll(newScroll);
  });

  return $view;
};
