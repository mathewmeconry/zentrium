const path = require('path');

module.exports = function (config) {
  config.entry['zentrium_timesheet'] = path.join(__dirname, 'less/bundle.less');
  return config;
}
