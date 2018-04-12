const path = require('path');

module.exports = function (config) {
  config.entry['zentrium_map'] = [
    path.join(__dirname, 'less/bundle.less'),
    path.join(__dirname, 'js/projections.js'),
    path.join(__dirname, 'js/map.js'),
    path.join(__dirname, 'js/map_edit.js'),
  ];
  return config;
}
