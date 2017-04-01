const {mix} = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    //.version()
    .copy('resources/assets/img/*', 'public/img');

mix.js('resources/assets/js/docs.js', 'public/js')
    .sass('resources/assets/sass/docs.scss', 'public/css')
    //.version()
    .copy('resources/assets/svg', 'public/svg')
    .copy('resources/assets/fonts', 'public/fonts')
    .browserSync({
        proxy: 'yap.dev/docs'
    });
