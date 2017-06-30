var Zentrium = Zentrium || {};
Zentrium.Schedule = Zentrium.Schedule || {};

// Emphasize quarter of a day
$.fullCalendar.views.timeline.resourceClass.prototype.instantiateGrid = _.wrap($.fullCalendar.views.timeline.resourceClass.prototype.instantiateGrid, function (func) {
  var grid = func.apply(this, arguments);
  grid.slatCellHtml = _.wrap(grid.slatCellHtml, function (func) {
    var args = [].slice.call(arguments, 1);
    classes = [];
    if(args[0].hour() === 0 && args[0].minute() === 0) {
      classes.push('schedule-slat-day');
    }
    if(args[0].hour() % 6 === 0 && args[0].minute() === 0) {
      classes.push('schedule-slat-quarter');
    }
    html = func.apply(this, args);
    html = html.replace('class="', 'class="' + classes.join(' ') + ' ');
    return html;
  });
  return grid;
});

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

      var eventPrototype = {
        id: generateId(),
        resourceId: resource.id,
        start: start,
        end: end,
        className: 'schedule-operation-pending',
        editable: false,
      };

      var updateHelper = function (events, time) {
        if(!time || (resourceUpdates[resource.id] || 0) < time) {
          resourceUpdates[resource.id] = time;
          $view.fullCalendar('removeEvents', function (eventData) {
            return eventData.resourceId == resource.id;
          });
          for(var i in events) {
            $view.fullCalendar('renderEvent', events[i]);
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

  var view = $view.fullCalendar('getView');
  view.displayEvents = Zentrium.Schedule.pause(view.displayEvents);

  $(window).keydown(function(e) {
    if(e.keyCode != 37 && e.keyCode != 39) { // left/right arrow keys
      return;
    }
    var view = $view.fullCalendar('getView');
    var slotWidth = view.timeGrid.slotWidth;
    var oldScroll = view.queryScroll();
    var newScroll = { top: oldScroll.top };
    if(e.keyCode == 37) {
      newScroll.left = Math.round((oldScroll.left - slotWidth) / slotWidth) * slotWidth;
    } else {
      newScroll.left = Math.round((oldScroll.left + slotWidth) / slotWidth) * slotWidth;
    }
    view.applyScroll(newScroll);
  });

  return $view;
};
