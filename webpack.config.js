const path = require('path');
const fs = require('fs');
const npm = require('npm');
const Encore = require('@symfony/webpack-encore');

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
module.exports = new Promise((resolve, reject) => {
  npm.load({}, (err, manager) => {
    if (err) return reject(err);
    manager.commands.ls([], true, (err, package) => {
      if (err) return reject(err);
      resolve(package);
    });
  });
}).then(package => {
  for(let d in package.dependencies) {
    const webpackPath = path.join(package.dependencies[d].path, 'webpack.js');
    if (fs.existsSync(webpackPath)) {
      webpackConfig = require(webpackPath)(webpackConfig);
    }
  }
  return webpackConfig;
});
