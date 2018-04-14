const path = require('path');

module.exports = function (config) {
  config.entry['vkaf_oaf'] = [
    path.join(__dirname, 'js/kiosk.js'),
    path.join(__dirname, 'js/kiosk_schedule.js'),
    path.join(__dirname, 'js/resource.js'),
    path.join(__dirname, 'js/schedule.js'),
    path.join(__dirname, 'js/user_desk.js'),
    path.join(__dirname, 'less/bundle.less'),
  ];

  return config;
}
