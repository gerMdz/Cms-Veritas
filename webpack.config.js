const Encore = require('@symfony/webpack-encore');
const PurgeCssPlugin = require('purgecss-webpack-plugin');
const glob = require('glob-all');
const path = require('path');
const fs = require( 'fs' );
const { loaders } = require( '@ckeditor/ckeditor5-dev-utils' );
const { CKEditorTranslationsPlugin } = require( '@ckeditor/ckeditor5-dev-translations' );

function getCKEditorConfig(webroot) {
    return {
        devtool: 'source-map',
        performance: { hints: false },
        cache: {
            type: 'filesystem',
            cacheDirectory: path.resolve(__dirname, 'var/cache/js/webpack'),
        },
        entry: path.resolve( __dirname, webroot + 'app/assets/libraries/ckeditor/src', 'ckeditor.ts' ),
        output: {
            library: 'ClassicEditor',
            path: path.resolve( __dirname, webroot + 'media/libraries/ckeditor' ),
            filename: 'ckeditor.js',
            libraryTarget: 'umd',
            libraryExport: 'default'
        },
        plugins: [
            new CKEditorTranslationsPlugin( {
                language: 'en',
                additionalLanguages: 'all'
            } )
        ],
        module: {
            rules: [
                loaders.getIconsLoader( { matchExtensionOnly: true } ),
                loaders.getStylesLoader( {
                    themePath: require.resolve( '@ckeditor/ckeditor5-theme-lark' ),
                    minify: true
                } ),
                loaders.getTypeScriptLoader()
            ]
        },
        resolve: {
            extensions: [ '.ts', '.js', '.json' ]
        },
        optimization: {
            removeAvailableModules: false,
            removeEmptyChunks: false,
            splitChunks: false,
        },
    };
}

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('perfil', './assets/js/perfil.js')
    .addEntry('section_select', './assets/js/select_section.js')
    .addEntry('select2', './assets/js/select2.js')
    .addEntry('select2-parent', './assets/js/select2-parent.js')
    .addEntry('select2-entrada', './assets/js/select2-entrada.js')
    .addEntry('ckeditor_config', './assets/ckeditor/config.js')
    .addEntry('nota_mensaje', './assets/js/nota_mensaje.js')
    .addStyleEntry('loginStyle', './assets/css/styles.css')
    .addStyleEntry('perfilStyle', './assets/css/account.css')

    //.addEntry('page2', './assets/js/page2.js')

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

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    .enableSassLoader((options) => {
        options.sourceMap = true;
        options.sassOptions = {
            outputStyle: options.outputStyle,
            sourceComments: !Encore.isProduction(),
        };
        delete options.outputStyle;
    }, {})

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()


    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
    .enablePostCssLoader()

    .enablePostCssLoader((options) => {
        // new option outlined here https://webpack.js.org/loaders/postcss-loader/
        options.postcssOptions = {
            config: './postcss.config.js',
        }
    })

    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
        pattern: /\.(png|jpg|jpeg)$/,

    })
    // .copyFiles([
    //     {from: './node_modules/@ckeditor/ckeditor5-build-classic/build/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
    //     {from: './node_modules/@ckeditor/ckeditor5-adapter-ckfinder/build/adapter-ckfinder.js ', to: 'ckeditor/adapters/[path][name].[ext]'},
    //     // {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
    //     // {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
    //     // {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'},
    //     // {from: './assets/ckeditor/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
    // ])


;
// if (Encore.isProduction()) {
//     Encore.addPlugin(new PurgeCssPlugin({
//         paths: glob.sync([
//             path.join(__dirname, 'templates/**/*.html.twig')
//         ]),
//         defaultExtractor: (content) => {
//             return content.match(/[\w-/:]+(?<!:)/g) || [];
//         }
//     }));
// }

let webroot = '';
if (!fs.existsSync('app/release_metadata.json')) {
    let files = glob.sync("**/app/release_metadata.json");
    if (files.length > 0) {
        webroot = path.dirname(path.dirname(files[0])) + '/';
    }
}

module.exports = Encore.getWebpackConfig();
