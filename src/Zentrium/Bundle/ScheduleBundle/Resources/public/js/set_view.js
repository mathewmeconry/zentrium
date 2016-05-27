$(function() {
  var $view = $('#schedule-requirement-set');
  if(!$view.length) {
    return;
  }

  var config = $view.data('config');

  $('#schedule-requirement-set').fullCalendar({
    now: config.begin,
    editable: false,
    contentHeight: 'auto',
    resourceAreaWidth: '20%',
    scrollTime: '00:00',
    header: false,
    defaultView: 'timelineCustom',
    views: {
      timelineCustom: {
        type: 'timeline',
        duration: { seconds: config.duration }
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
    resources: config.resources,
    events: config.events,
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
  });
});
