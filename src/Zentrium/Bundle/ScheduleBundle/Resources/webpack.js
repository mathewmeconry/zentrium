const path = require('path');

module.exports = function (config) {
  config.entry['zentrium_schedule'] = [
    'moment',
    'fullcalendar',
    'fullcalendar/dist/fullcalendar.min.css',
    'fullcalendar-scheduler',
    'fullcalendar-scheduler/dist/scheduler.min.css',
    'fullcalendar/dist/locale/de.js', // contains translations for Moment.js
    path.join(__dirname, 'js/schedule_validate.js'),
    path.join(__dirname, 'js/schedule_view.js'),
    path.join(__dirname, 'js/set_view.js'),
    path.join(__dirname, 'less/bundle.less'),
  ];

  return config;
}
