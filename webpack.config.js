var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
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
