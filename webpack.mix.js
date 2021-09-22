const mix = require('laravel-mix');
const path = require('path');

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

mix.webpackConfig({
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
    publicPath: '/',
  },

  resolve:{
    alias: {
      components: path.resolve(__dirname, './resources/js/components'),
      mixins: path.resolve(__dirname, './resources/js/mixins'),
      sass: path.resolve(__dirname, './resources/js/sass')
    }
  }
});

mix.sass('resources/sass/app.scss', 'public/css')
    .options({
        processCssUrls: false
    });

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .version();
