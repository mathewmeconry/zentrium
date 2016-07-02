$(function () {
  var $schedule = $('#oaf-user-schedule');
  var config = $schedule.data('config');
  if(!_.isObject(config)) {
    return;
  }

  var scheduleOptions = {
    defaultView: 'agendaMultiDay',
    defaultDate: config.begin,
    views: {
      agendaMultiDay: {
        type: 'agenda',
        duration: { days: config.dayCount },
        allDaySlot: false,
        slotDuration: { seconds: config.slotDuration },
      }
    },
    eventSources: [
      config.shifts,
      config.availabilities,
    ],
    eventAfterRender: function(event, element, view) {
      var buttons = '<a href="' + event.timesheet + '" class="btn btn-default"><i class="fa fa-clock-o"></i></a>';
      $(element).popover({
        placement: 'top',
        trigger: 'manual',
        html: true,
        container: 'body',
        content: '<div class="btn-group less-padding">' + buttons + '</div>',
      });
    },
    eventClick: function(calEvent, jsEvent, view) {
      $schedule.find('.fc-event').not(this).popover('hide');
      $(this).popover('toggle');
    },
    dayClick: function(date, jsEvent, view) {
      $schedule.find('.fc-event').popover('hide');
    },
  };

  Zentrium.Schedule.setup($schedule, {}, scheduleOptions);
});
