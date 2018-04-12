const path = require('path');

module.exports = function (config) {
  config.entry['zentrium_log'] = [
    path.join(__dirname, 'js/bundle.js'),
    path.join(__dirname, 'less/bundle.less'),
  ];
  return config;
}
