const path = require('path');
const ServiceWorkerWebpackPlugin = require('serviceworker-webpack-plugin');

module.exports = function (config) {
  config.entry['vkaf_oaf'] = [
    path.join(__dirname, 'js/kiosk.js'),
    path.join(__dirname, 'js/push.js'),
    path.join(__dirname, 'js/resource.js'),
    path.join(__dirname, 'js/user_desk.js'),
    path.join(__dirname, 'less/bundle.less'),
  ];

  config.entry['vkaf_oaf_schedule'] = [
    path.join(__dirname, 'js/kiosk_schedule.js'),
    path.join(__dirname, 'js/schedule.js'),
  ];

  config.plugins.push(new ServiceWorkerWebpackPlugin({
    entry: path.join(__dirname, 'js/worker.js'),
    publicPath: config.output.publicPath,
  }));

  return config;
}
