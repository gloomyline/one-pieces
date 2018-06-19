/**
 * @Date:   2017-12-14T17:05:37+08:00
 * @Last modified time: 2018-01-25T15:25:32+08:00
 */
// see http://vuejs-templates.github.io/webpack for documentation.
var path = require('path')

module.exports = {
  build: {
    env: require('./prod.env'),
    // index: path.resolve(__dirname, '../dist/index.html'),
    // assetsRoot: path.resolve(__dirname, '../dist'),
    // assetsSubDirectory: 'static',
    // assetsPublicPath: '/',
    index: path.resolve(__dirname, '../../../php/wkdk/frontend/web/m/index.html'),
    assetsRoot: path.resolve(__dirname, '../../../php/wkdk/frontend/web/m'),
    assetsSubDirectory: 'static',
    assetsPublicPath: '/m/',
    productionSourceMap: true,
    // Gzip off by default as many popular static hosts such as
    // Surge or Netlify already gzip all static assets for you.
    // Before setting to `true`, make sure to:
    // npm install --save-dev compression-webpack-plugin
    productionGzip: false,
    productionGzipExtensions: ['js', 'css'],
    // Run the build command with an extra argument to
    // View the bundle analyzer report after build finishes
    // `npm run build --report`
    // Set to `true` or `false` to always turn it on or off
    bundleAnalyzerReport: process.env.npm_config_report,
    apiConfig: {
      urlPrefix: 'http://fenqi-m.wkdk.cn/',
      apiUrlPrefix: 'http://fenqi-api.wkdk.cn/',
      logger: false
    }
  },
  dev: {
    env: require('./dev.env'),
    port: 8080,
    autoOpenBrowser: false,
    assetsSubDirectory: 'static',
    assetsPublicPath: '/',
    proxyTable: {},
    // CSS Sourcemaps off by default because relative paths are "buggy"
    // with this option, according to the CSS-Loader README
    // (https://github.com/webpack/css-loader#sourcemaps)
    // In our experience, they generally work as expected,
    // just be aware of this issue when enabling this option.
    cssSourceMap: false,
    apiConfig: {
      // urlPrefix: 'http://fenqi-m.wkdk.com',
      urlPrefix: 'http://192.168.0.188:8082/',
      apiUrlPrefix: 'http://fenqi-api.wkdk.com/',
      logger: true
    }
  }
}
