const path = require('path');
const fs = require('fs');
const Encore = require('@symfony/webpack-encore');
const package = require('./package.json');

Encore
  .setOutputPath('web/build/')
  .setPublicPath('/build')
  .addAliases({
    '@willdurand/js-translation-bundle$': path.join(__dirname, 'vendor/willdurand/js-translation-bundle/Resources/js/translator.js'),
  })
  .addEntry('dummy', './dummy.js')
  .enableLessLoader()
  .enableSourceMaps(!Encore.isProduction())
  .cleanupOutputBeforeBuild()
  .enableVersioning()
;

let webpackConfig = Encore.getWebpackConfig();
delete webpackConfig.entry['dummy'];

// let direct dependencies extend the configuration
for(let dependency in package.dependencies) {
  const configPath = path.join(__dirname, 'node_modules', dependency, 'webpack.js');
  if (fs.existsSync(configPath)) {
    webpackConfig = require(configPath)(webpackConfig);
  }
}

module.exports = webpackConfig;
