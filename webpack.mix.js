let mix = require('laravel-mix');

require('./nova.mix');

mix.setPublicPath('dist')
    .js('resources/js/field.js', 'js')
    .vue({ version: 3 })
    .nova('dive-be/nova-froala-field')
    .sass('resources/scss/field.scss', 'css')
    .copy('node_modules/froala-editor/css/froala_style.min.css', 'dist/css/froala_styles.min.css')
    .webpackConfig({
        output: {
            publicPath: '/',
            chunkFilename: 'vendor/nova/froala/[name].js',
        },
        resolve: {
            symlinks: false,
            fallback: {
                crypto: require.resolve('crypto-browserify'),
                stream: require.resolve('stream-browserify'),
            },
        },
    });
