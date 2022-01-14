const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    .copyFiles([{
        from: './assets/ext',
        // optional target path, relative to the output dir
        to: 'ext/[path][name].[ext]',
    }, {
        from: './assets/img',
        to: 'img/[name].[ext]',
    }])

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('index', './assets/js/index.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    .addEntry('grid', './assets/js/grid/index.js')
    .addEntry('transferList', './assets/js/transferList/index.js')
    .addEntry('secondaryAccount', './assets/js/secondaryAccount/index.js')
    .addEntry('treeLastSixMonths', './assets/js/treeLastSixMonths/index.js')
    .addEntry('tree', './assets/js/tree/index.js')
    .addEntry('settingsCategory', './assets/js/settingsCategory/index.js')
    .addEntry('settingsCategoryAdd', './assets/js/settingsCategory/add.js')
    .addEntry('settingsAssignment', './assets/js/settingsAssignment/index.js')
    .addEntry('settingsAccount', './assets/js/settingsAccount/index.js')
    .addEntry('settingsAccountEditSubaccount', './assets/js/settingsAccount/editSubaccount.js')
    .addEntry('settingsGui', './assets/js/settingsGui/index.js')
    .addEntry('chart', './assets/js/chart.js')
    .addEntry('paypal', './assets/js/paypal/index.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    // .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // .configureBabel((config) => {
    //     config.plugins.push('@babel/plugin-proposal-class-properties');
    // })

    // enables @babel/preset-env polyfills
    // .configureBabelPresetEnv((config) => {
    //     config.useBuiltIns = 'usage';
    //     config.corejs = 3;
    // })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
