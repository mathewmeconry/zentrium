const path = require('path');
const glob = require('glob');
const webpack = require('webpack');

module.exports = function (config) {
  config.plugins.push(new webpack.ProvidePlugin({
    'jQuery': 'jquery',
    'Sortable': 'sortablejs',
    'Translator': ['zentrium', 'Translator'],
  }));

  config.resolve.alias['zentrium$'] = path.join(__dirname, 'js/lib.js');

  config.entry['zentrium'] = glob.sync('./web/js/translations/*/*.js').concat([
    'bootstrap',
    'bootstrap-datepicker',
    'bootstrap-datepicker/dist/locales/bootstrap-datepicker.de.min.js',
    'select2',
    'select2/dist/js/i18n/de.js',
    'jquery-slimscroll',
    'jquery-minicolors',
    'sortablejs/jquery.binding.js',
    'admin-lte',
    path.join(__dirname, 'js/global.js'),
    path.join(__dirname, 'js/layout.js'),
    path.join(__dirname, 'js/form.js'),
    path.join(__dirname, 'less/bundle.less'),
  ]);

  return config;
}
