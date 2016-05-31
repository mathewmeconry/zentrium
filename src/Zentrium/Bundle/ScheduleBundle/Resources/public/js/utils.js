var Zentrium = Zentrium || {};
Zentrium.Schedule = Zentrium.Schedule || {};

Zentrium.Schedule.pause = function (func, paused) {
  var lastArgs;
  var lastThis;
  var queue = false;

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

Zentrium.Schedule.setup = function ($view, parameters, config, selectCallback) {
  if(!$view.length) {
    return;
  }

  var generateId = (function () {
    var id = 0;
    return function () {
      return 'tmp' + (++id);
    };
  })();

  var resourceUpdates = {};

  $view.fullCalendar($.extend({}, {
    now: parameters.begin || null,
    editable: false,
    selectable: false,
    selectHelper: true,
    select: function(start, end, jsEvent, view, resource) {
      $view.fullCalendar('unselect');

      if(!view.displayEvents.start) {
        view.displayEvents = Zentrium.Schedule.pause(view.displayEvents);
      }

      var eventPrototype = {
        id: generateId(),
        resourceId: resource.id,
        start: start,
        end: end,
        className: 'schedule-operation-pending',
      };

      var updateHelper = function (events, time) {
        if(!time || (resourceUpdates[resource.id] || 0) < time) {
          resourceUpdates[resource.id] = time;
          $view.fullCalendar('removeEvents', function (eventData) {
            return eventData.resourceId == resource.id;
          });
          for(var i in events) {
            $view.fullCalendar('renderEvent', events[i], true);
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
};
