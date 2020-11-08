var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableSingleRuntimeChunk()
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/scss/app.scss')
    .enableSassLoader()
    .autoProvidejQuery()
    .copyFiles({
        from: './assets/img',
        to: 'images/[path][name].[ext]',
    })
    .copyFiles({
        from: 'node_modules/leaflet-extra-markers/dist/img/',
        to: 'images/extramarkers/[name].[ext]',
    })
    .enableVersioning()
;

module.exports = Encore.getWebpackConfig();
